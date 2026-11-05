<?php

namespace App\Http\Controllers;

use App\Models\{Shift, ShiftAssignment, SurgicalTeam, ActivityLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController
{
    /**
     * Otomatis mengubah kolom staff_role dari ENUM('Dinas','On Call')
     * menjadi VARCHAR(50) agar bisa menyimpan 'RR' tanpa error 500.
     */
    private function ensureRrColumnSupport(): void
    {
        try {
            DB::statement("ALTER TABLE shift_assignments MODIFY COLUMN staff_role VARCHAR(50) NOT NULL DEFAULT 'Dinas'");
        } catch (\Throwable $e) {
            // Abaikan jika sudah diubah atau menggunakan driver selain MySQL
        }
    }

    public function index(Request $r)
    {
        $date = $r->input('date', now('Asia/Jakarta')->toDateString());
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
            $date = now('Asia/Jakarta')->toDateString();
        }

        return view('admin.shifts.index', [
            'date' => $date,
            'shifts' => Shift::with([
                'assignments' => fn($q) => $q->whereDate('shift_date', $date)
                    ->orderBy('staff_role')
                    ->orderBy('staff_name')
            ])->orderBy('id')->get(),
            'teams' => SurgicalTeam::with('profession')
                ->where('active', true)
                ->orderBy('name')
                ->get()
        ]);
    }

    public function manage(Request $r)
    {
        $shifts = Shift::withCount(['assignments' => fn($q) => $q->whereDate('shift_date', now('Asia/Jakarta')->toDateString())])
            ->orderBy('start_time')
            ->get();
        $editShift = null;
        if ($r->filled('edit')) {
            $editShift = Shift::find((int) $r->query('edit'));
        }

        return view('admin.shifts.manage', compact('shifts', 'editShift'));
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'color' => 'nullable|string|max:20',
            'active' => 'nullable|boolean',
            'note' => 'nullable|string|max:1000'
        ]);

        $d['active'] = $r->has('active') ? $r->boolean('active') : true;
        $d['color'] = $d['color'] ?: '#1b7a5a';

        $newShift = Shift::create($d);
        ActivityLog::record('INPUT', 'Shift', "Tambah shift baru: {$newShift->name} ({$newShift->start_time} - {$newShift->end_time})", [
            'record_id'  => $newShift->id,
            'new_values' => $newShift->toArray(),
        ]);

        return redirect()->route('admin.shifts.manage')->with('success', 'Shift baru berhasil ditambahkan.');
    }

    public function update(Request $r, Shift $shift)
    {
        $d = $r->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required',
            'end_time' => 'required',
            'color' => 'nullable|string|max:20',
            'active' => 'nullable|boolean',
            'note' => 'nullable|string|max:1000'
        ]);

        $d['active'] = $r->has('active') ? $r->boolean('active') : true;
        $d['color'] = $d['color'] ?: '#1b7a5a';

        $old = $shift->toArray();
        $shift->update($d);

        ActivityLog::record('UBAH', 'Shift', "Ubah data shift: {$shift->name} ({$shift->start_time} - {$shift->end_time})", [
            'record_id'  => $shift->id,
            'old_values' => $old,
            'new_values' => $shift->toArray(),
        ]);

        return redirect()->route('admin.shifts.manage')->with('success', 'Data shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        $snapshot = $shift->toArray();
        DB::transaction(function () use ($shift) {
            // Hapus semua penugasan staf yang terikat pada shift ini terlebih dahulu
            $shift->assignments()->delete();
            // Hapus shift-nya
            $shift->delete();
        });

        ActivityLog::record('HAPUS', 'Shift', "Hapus shift: {$shift->name}", [
            'record_id'  => $shift->id,
            'old_values' => $snapshot,
        ]);

        return back()->with('success', 'Shift berhasil dihapus.');
    }

    public function storeAssignment(Request $r, Shift $shift)
    {
        $this->ensureRrColumnSupport();

        $d = $r->validate([
            'shift_date' => 'required|date',
            'staff_names' => 'nullable|array',
            'staff_names.*' => 'nullable|string|max:120',
            'staff_name' => 'nullable|string|max:120',
            'staff_role' => 'required|in:Dinas,RR,On Call',
        ]);

        $names = collect($d['staff_names'] ?? [])
            ->when(!empty($d['staff_name']), fn($c) => $c->push($d['staff_name']))
            ->map(fn($n) => trim((string) $n))
            ->filter()
            ->unique(fn($n) => mb_strtolower($n));

        if ($names->isEmpty()) {
            return back()->with('error', 'Pilih minimal satu nama petugas.');
        }

        $allowed = SurgicalTeam::where('active', true)
            ->pluck('name')
            ->mapWithKeys(fn($n) => [mb_strtolower(trim($n)) => $n]);

        $saved = 0;
        foreach ($names as $name) {
            $canonical = $allowed[mb_strtolower($name)] ?? $name;
            ShiftAssignment::firstOrCreate([
                'shift_id' => $shift->id,
                'shift_date' => $d['shift_date'],
                'staff_name' => $canonical,
                'staff_role' => $d['staff_role'],
            ]);
            $saved++;
        }

        return back()->with('success', $saved . ' petugas berhasil ditambahkan ke shift ' . $shift->name . '.');
    }

    public function storeAssignments(Request $r)
    {
        $this->ensureRrColumnSupport();

        $payload = $r->input('assignments', []);
        if (!is_array($payload) || empty($payload)) {
            return back()->with('error', 'Tidak ada data shift yang dikirim.');
        }

        $allowed = SurgicalTeam::where('active', true)
            ->pluck('name')
            ->mapWithKeys(fn($n) => [mb_strtolower(trim($n)) => $n]);

        $saved = 0;
        $notesUpdated = 0;
        $shiftsUpdated = 0;
        $redirectDate = null;

        foreach ($payload as $shiftId => $row) {
            $shift = Shift::find($shiftId);
            if (!$shift || !is_array($row)) {
                continue;
            }

            // Update Jam Shift (start_time & end_time) jika dikirim dari form
            $shiftUpdates = [];
            if (array_key_exists('start_time', $row) && !empty($row['start_time'])) {
                $startTime = trim($row['start_time']);
                if ($shift->start_time !== $startTime) {
                    $shiftUpdates['start_time'] = $startTime;
                }
            }
            if (array_key_exists('end_time', $row) && !empty($row['end_time'])) {
                $endTime = trim($row['end_time']);
                if ($shift->end_time !== $endTime) {
                    $shiftUpdates['end_time'] = $endTime;
                }
            }

            // Update Catatan Shift jika ada perubahan
            if (array_key_exists('note', $row)) {
                $note = trim((string) ($row['note'] ?? '')) ?: null;
                if ($shift->note !== $note) {
                    $shiftUpdates['note'] = $note;
                    $notesUpdated++;
                }
            }

            if (!empty($shiftUpdates)) {
                $shift->update($shiftUpdates);
                if (array_key_exists('start_time', $shiftUpdates) || array_key_exists('end_time', $shiftUpdates)) {
                    $shiftsUpdated++;
                }
            }

            $date = $row['shift_date'] ?? null;
            if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date)) {
                continue;
            }
            $redirectDate = $redirectDate ?: $date;

            $role = in_array($row['staff_role'] ?? 'Dinas', ['Dinas', 'RR', 'On Call'], true)
                ? $row['staff_role']
                : 'Dinas';

            $names = collect($row['staff_names'] ?? [])
                ->when(!empty($row['staff_name']), fn($c) => $c->push($row['staff_name']))
                ->map(fn($n) => trim((string) $n))
                ->filter()
                ->unique(fn($n) => mb_strtolower($n));

            foreach ($names as $name) {
                $canonical = $allowed[mb_strtolower($name)] ?? $name;
                $created = ShiftAssignment::firstOrCreate([
                    'shift_id' => $shift->id,
                    'shift_date' => $date,
                    'staff_name' => $canonical,
                    'staff_role' => $role,
                ]);
                if ($created->wasRecentlyCreated) {
                    $saved++;
                }
            }
        }

        if ($saved === 0 && $notesUpdated === 0 && $shiftsUpdated === 0) {
            return back()->with('error', 'Belum ada perubahan atau nama petugas yang dipilih.');
        }

        return redirect()
            ->route('admin.shifts.index', ['date' => $redirectDate ?: now('Asia/Jakarta')->toDateString()])
            ->with('success', 'Jadwal shift berhasil disimpan (' . $saved . ' petugas ditambahkan' . ($notesUpdated ? ', catatan diperbarui' : '') . ($shiftsUpdated ? ', jam shift diperbarui' : '') . ').');
    }

    public function storeBulkAssignments(Request $r)
    {
        return $this->storeAssignments($r);
    }

    public function destroyAssignment(Shift $shift, ShiftAssignment $assignment)
    {
        ActivityLog::record('HAPUS', 'Shift', "Hapus petugas {$assignment->staff_name} ({$assignment->staff_role}) dari shift {$shift->name} tanggal " . (\Carbon\Carbon::parse($assignment->shift_date)->format('d/m/Y')), [
            'old_values' => $assignment->toArray(),
        ]);

        $assignment->delete();
        return back()->with('success', 'Petugas dihapus.');
    }

    public function copy(Request $r)
    {
        $this->ensureRrColumnSupport();

        $d = $r->validate([
            'source_date' => 'required|date',
            'target_date' => 'required|date'
        ]);

        $src = ShiftAssignment::whereDate('shift_date', $d['source_date'])->get();
        if ($src->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal shift pada tanggal sumber.');
        }

        $copied = 0;
        foreach ($src as $a) {
            $created = ShiftAssignment::firstOrCreate([
                'shift_id' => $a->shift_id,
                'shift_date' => $d['target_date'],
                'staff_name' => $a->staff_name,
                'staff_role' => $a->staff_role
            ]);
            if ($created->wasRecentlyCreated) {
                $copied++;
            }
        }

        return redirect()
            ->route('admin.shifts.index', ['date' => $d['target_date']])
            ->with('success', "Jadwal shift berhasil disalin ($copied petugas ditambahkan).");
    }

    public function exportCsv(Request $r)
    {
        $date = $r->input('date', now('Asia/Jakarta')->toDateString());
        $shifts = Shift::with([
            'assignments' => fn($q) => $q->whereDate('shift_date', $date)
                ->orderBy('staff_role')
                ->orderBy('staff_name')
        ])->orderBy('id')->get();

        $h = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=jadwal-shift-$date.xls"
        ];

        $esc = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        $rows = '';

        foreach ($shifts as $i => $s) {
            $dinas = $s->assignments->where('staff_role', 'Dinas')->pluck('staff_name')->join(', ');
            $rr = $s->assignments->where('staff_role', 'RR')->pluck('staff_name')->join(', ');
            $oncall = $s->assignments->where('staff_role', 'On Call')->pluck('staff_name')->join(', ');

            $rows .= '<tr>'
                . '<td style="text-align:center">' . ($i + 1) . '</td>'
                . '<td style="font-weight:bold">' . $esc($s->name) . '</td>'
                . '<td style="text-align:center">' . $esc(substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5)) . '</td>'
                . '<td>' . $esc($dinas ?: '-') . '</td>'
                . '<td>' . $esc($rr ?: '-') . '</td>'
                . '<td>' . $esc($oncall ?: '-') . '</td>'
                . '<td>' . $esc($s->note ?: '-') . '</td>'
                . '</tr>';
        }

        $html = "\xEF\xBB\xBF" . '<html><head><meta charset="UTF-8"><style>'
            . 'table{border-collapse:collapse;width:100%;font-family:Arial,sans-serif;font-size:11pt}'
            . 'th,td{border:1px solid #333;padding:7px;vertical-align:top}'
            . 'th{background:#1b7a5a;color:#fff;font-weight:bold;text-align:center}'
            . '.title{font-size:14pt;font-weight:bold;border:none;padding:4px 0}'
            . '.sub{font-size:10pt;border:none;padding:0 0 10px 0;color:#444}'
            . '</style></head><body>'
            . '<table>'
            . '<tr><td colspan="7" class="title">JADWAL SHIFT KAMAR OPERASI — RS MUTIARA AINI</td></tr>'
            . '<tr><td colspan="7" class="sub">Tanggal: ' . $esc($date) . '</td></tr>'
            . '<thead><tr><th>No</th><th>Shift</th><th>Jam</th><th>Petugas Dinas</th><th>Petugas RR</th><th>Petugas On Call</th><th>Catatan Shift</th></tr></thead>'
            . '<tbody>' . ($rows ?: '<tr><td colspan="7" style="text-align:center">Tidak ada data shift</td></tr>') . '</tbody>'
            . '</table></body></html>';

        return response($html, 200, $h);
    }
}

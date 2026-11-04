<?php

namespace App\Http\Controllers;

use App\Models\{SurgerySchedule, Patient, Operator, Anesthesia, SurgicalTeam};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now('Asia/Jakarta')->toDateString());
        $schedules = SurgerySchedule::with(['patient', 'operator', 'anesthesia', 'surgicalTeams'])
            ->whereDate('operation_date', $date)->orderBy('operation_time')->get();
        return view('admin.schedules.index', compact('schedules', 'date'));
    }

    public function create(Request $request)
    {
        $schedule = new SurgerySchedule();
        return $this->form($schedule, $request->input('date', now('Asia/Jakarta')->toDateString()));
    }

    public function store(Request $r)
    {
        $data = $this->validateData($r);
        return DB::transaction(function () use ($r, $data) {
            $patient = $this->resolvePatient($r, null);
            $data['patient_id'] = $patient->id;
            $teamIds = $this->resolveTeamIds($data['surgical_team_ids'] ?? [], $data['surgical_team_names'] ?? []);
            unset($data['surgical_team_ids'], $data['surgical_team_names'], $data['patient_name'], $data['patient_medical_record_no'], $data['patient_gender']);
            $schedule = SurgerySchedule::create($data);
            $schedule->surgicalTeams()->sync($teamIds);
            return redirect()->route('admin.schedules.create', ['date' => $schedule->operation_date->toDateString()])
                ->with('success', 'Jadwal operasi berhasil disimpan. Silakan lanjut input jadwal berikutnya.');
        });
    }

    public function edit(SurgerySchedule $schedule)
    {
        $schedule->load('surgicalTeams', 'patient');
        return $this->form($schedule, $schedule->operation_date?->toDateString());
    }

    public function update(Request $r, SurgerySchedule $schedule)
    {
        $data = $this->validateData($r);
        return DB::transaction(function () use ($r, $data, $schedule) {
            $patient = $this->resolvePatient($r, $schedule->patient_id);
            $data['patient_id'] = $patient->id;
            $teamIds = $this->resolveTeamIds($data['surgical_team_ids'] ?? [], $data['surgical_team_names'] ?? []);
            unset($data['surgical_team_ids'], $data['surgical_team_names'], $data['patient_name'], $data['patient_medical_record_no'], $data['patient_gender']);
            $schedule->update($data);
            $schedule->surgicalTeams()->sync($teamIds);
            return redirect()->route('admin.schedules.manage', ['date' => $schedule->operation_date->toDateString()])
                ->with('success', 'Jadwal operasi diperbarui.');
        });
    }

    public function destroy(SurgerySchedule $schedule)
    {
        $date = $schedule->operation_date->toDateString();
        $schedule->delete();
        return redirect()->route('admin.schedules.manage', ['date' => $date])->with('success', 'Jadwal dihapus.');
    }

    public function calendarEvents(Request $r)
    {
        $query = SurgerySchedule::with(['patient', 'surgicalTeams'])->when($r->start, fn($q) => $q->whereDate('operation_date', '>=', substr($r->start, 0, 10)))->when($r->end, fn($q) => $q->whereDate('operation_date', '<=', substr($r->end, 0, 10)))->get();
        return $query->map(fn($s) => ['id' => $s->id, 'title' => $s->operation_time . ' • ' . $s->patient->name . ' • ' . $s->ok_level, 'start' => $s->start_date_time, 'end' => $s->end_date_time, 'url' => route('admin.schedules.edit', $s), 'editable' => auth()->user()->canAccess('schedule.manage'), 'backgroundColor' => $s->cito ? '#dc3545' : match ($s->ok_level) {
            'OK 2' => '#16a34a',
            'OK 3' => '#9333ea',
            default => '#0d6efd'
        }, 'borderColor' => $s->cito ? '#b02a37' : match ($s->ok_level) {
            'OK 2' => '#15803d',
            'OK 3' => '#7e22ce',
            default => '#0b5ed7'
        }, 'extendedProps' => ['diagnosis' => $s->diagnosis, 'procedure' => $s->procedure, 'ok' => $s->ok_level, 'cito' => $s->cito, 'team' => $s->surgical_team_names]])->values();
    }

    public function move(Request $r, SurgerySchedule $schedule)
    {
        abort_unless(auth()->user()->canAccess('schedule.manage'), 403);
        $data = $r->validate(['operation_date' => 'required|date', 'operation_time' => 'required|date_format:H:i']);
        $schedule->update($data);
        return response()->json(['ok' => true, 'message' => 'Jadwal berhasil dipindahkan.']);
    }

    public function print(Request $request)
    {
        $date = $request->input('date', now('Asia/Jakarta')->toDateString());
        $schedules = SurgerySchedule::with(['patient', 'operator', 'anesthesia', 'surgicalTeams'])->whereDate('operation_date', $date)->orderBy('operation_time')->get();
        return view('admin.schedules.print', compact('schedules', 'date'));
    }

    public function manage(Request $request)
    {
        $date = $request->input('date', now('Asia/Jakarta')->toDateString());
        $schedules = SurgerySchedule::with(['patient', 'operator', 'anesthesia', 'surgicalTeams'])->whereDate('operation_date', $date)->orderBy('operation_time')->get();
        $previous = Carbon::parse($date, 'Asia/Jakarta')->subDay()->toDateString();
        $next = Carbon::parse($date, 'Asia/Jakarta')->addDay()->toDateString();
        $today = now('Asia/Jakarta')->toDateString();
        return view('admin.schedules.manage', compact('schedules', 'date', 'previous', 'next', 'today'));
    }

    public function copy(Request $request)
    {
        $data = $request->validate(['source_date' => 'required|date', 'target_date' => 'required|date|different:source_date']);
        $source = $data['source_date'];
        $target = $data['target_date'];
        $schedules = SurgerySchedule::with('surgicalTeams')->whereDate('operation_date', $source)->orderBy('operation_time')->get();
        if ($schedules->isEmpty()) return back()->with('error', 'Tidak ada jadwal operasi pada tanggal sumber.');
        DB::transaction(function () use ($schedules, $target) {
            foreach ($schedules as $s) {
                $payload = $s->only(['patient_id', 'operator_id', 'anesthesia_id', 'operation_time', 'ok_level', 'diagnosis', 'procedure', 'cito', 'status', 'notes']);
                $payload['operation_date'] = $target;
                $payload['status'] = 'Terjadwal';
                $teamIds = $s->surgicalTeams->pluck('id')->map(fn($id) => (int)$id)->all();
                $new = SurgerySchedule::create($payload);
                $new->surgicalTeams()->sync($teamIds);
            }
        });
        return redirect()->route('admin.schedules.manage', ['date' => $target])->with('success', 'Jadwal operasi berhasil disalin ke ' . date('d-m-Y', strtotime($target)) . ' dan tersimpan pada Manage Jadwal.');
    }

    public function export(Request $request): StreamedResponse
    {
        $date = $request->input('date', now('Asia/Jakarta')->toDateString());
        $rows = SurgerySchedule::with(['patient', 'operator', 'anesthesia', 'surgicalTeams'])->whereDate('operation_date', $date)->orderBy('operation_time')->get();
        $filename = 'Jadwal_Operasi_' . date('Y-m-d', strtotime($date)) . '.xls';
        return response()->streamDownload(function () use ($rows) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><Worksheet ss:Name="Jadwal Operasi"><Table>';
            $headers = ['No', 'Tanggal', 'Jam', 'OK', 'Nama Pasien', 'No. RM', 'JK', 'Diagnosa', 'Tindakan', 'Operator', 'Anestesi', 'Tim Bedah', 'CITO', 'Status'];
            $xml .= '<Row>';
            foreach ($headers as $header) $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($header, ENT_XML1, 'UTF-8') . '</Data></Cell>';
            $xml .= '</Row>';
            foreach ($rows as $i => $s) {
                $values = [$i + 1, $s->operation_date?->format('d-m-Y'), substr((string)$s->operation_time, 0, 5), $s->ok_level, $s->patient?->name, $s->patient?->medical_record_no, $s->patient?->gender, $s->diagnosis, $s->procedure, $s->operator?->name, $s->anesthesia?->name, $s->surgical_team_names, $s->cito ? 'CITO' : '', $s->status];
                $xml .= '<Row>';
                foreach ($values as $value) {
                    $type = is_numeric($value) ? 'Number' : 'String';
                    $xml .= '<Cell><Data ss:Type="' . $type . '">' . htmlspecialchars((string)$value, ENT_XML1, 'UTF-8') . '</Data></Cell>';
                }
                $xml .= '</Row>';
            }
            $lastRow = $rows->count() + 1;
            $xml .= '</Table><AutoFilter x:Range="R1C1:R' . $lastRow . 'C14"/></Worksheet></Workbook>';
            echo $xml;
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="' . $filename . '"']);
    }

    private function form(SurgerySchedule $schedule, ?string $date = null)
    {
        return view('admin.schedules.form', compact('schedule') + ['date' => $date ?: now('Asia/Jakarta')->toDateString(), 'patients' => Patient::orderBy('name')->get(), 'operators' => Operator::where('active', true)->orderBy('name')->get(), 'anesthesias' => Anesthesia::where('active', true)->orderBy('name')->get(), 'teams' => SurgicalTeam::with('profession')->where('active', true)->orderBy('name')->get(), 'diagnosisHistory' => SurgerySchedule::whereNotNull('diagnosis')->where('diagnosis', '!=', '')->orderByDesc('updated_at')->pluck('diagnosis')->unique()->take(30)->values(), 'procedureHistory' => SurgerySchedule::whereNotNull('procedure')->where('procedure', '!=', '')->orderByDesc('updated_at')->pluck('procedure')->unique()->take(30)->values()]);
    }

    private function validateData(Request $r): array
    {
        return $r->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'patient_name' => 'required|string|max:255',
            'patient_medical_record_no' => 'nullable|string|max:255',
            'patient_gender' => 'required|in:P,L',
            'operation_date' => 'required|date',
            'operation_time' => 'required|date_format:H:i',
            'operator_id' => 'nullable|exists:operators,id',
            'anesthesia_id' => 'nullable|exists:anesthesias,id',
            'surgical_team_ids' => 'nullable|array',
            'surgical_team_ids.*' => 'integer|exists:surgical_teams,id',
            'surgical_team_names' => 'nullable|array',
            'surgical_team_names.*' => 'nullable|string|max:255',
            'ok_level' => 'required|in:OK 1,OK 2,OK 3',
            'diagnosis' => 'nullable|string',
            'procedure' => 'required|string|max:255',
            'cito' => 'nullable|boolean',
            'status' => 'required|in:Terjadwal,Selesai,Batal',
            'notes' => 'nullable|string'
        ]) + ['cito' => $r->boolean('cito'), 'surgical_team_ids' => array_values(array_unique(array_map('intval', $r->input('surgical_team_ids', []))))];
    }

    private function resolvePatient(Request $r, ?int $existingId = null): Patient
    {
        $name = trim((string)$r->input('patient_name'));
        $rm = trim((string)$r->input('patient_medical_record_no'));
        $gender = $r->input('patient_gender');
        $patient = $existingId ? Patient::find($existingId) : null;
        if (!$patient && $rm !== '') $patient = Patient::where('medical_record_no', $rm)->first();
        if (!$patient) $patient = Patient::whereRaw('LOWER(name)=?', [mb_strtolower($name)])->where('gender', $gender)->first();
        if (!$patient) {
            $patient = Patient::create(['medical_record_no' => $rm !== '' ? $rm : null, 'name' => $name, 'gender' => $gender, 'birth_date' => null, 'marital_status' => 'Belum Menikah']);
        } else {
            $patient->update(['medical_record_no' => $rm !== '' ? $rm : $patient->medical_record_no, 'name' => $name, 'gender' => $gender]);
        }
        return $patient;
    }

    private function resolveTeamIds(array $ids, array $names): array
    {
        $resolved = array_values(array_unique(array_map('intval', $ids)));
        foreach ($names as $name) {
            $name = trim((string)$name);
            if ($name === '') continue;
            $existing = SurgicalTeam::whereRaw('LOWER(name)=?', [mb_strtolower($name)])->first();
            if ($existing) {
                $resolved[] = $existing->id;
                continue;
            }
            $created = SurgicalTeam::create(['name' => $name, 'role' => 'Perawat', 'active' => true]);
            $resolved[] = $created->id;
        }
        return array_values(array_unique(array_map('intval', $resolved)));
    }
}

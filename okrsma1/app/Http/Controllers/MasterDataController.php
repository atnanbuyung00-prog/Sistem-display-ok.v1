<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Operator, Anesthesia, SurgicalTeam, Patient, Profession};

class MasterDataController
{
    private array $map = ['operators' => [Operator::class, 'Operator'], 'anesthesias' => [Anesthesia::class, 'Anestesi'], 'teams' => [SurgicalTeam::class, 'Tim Bedah'], 'patients' => [Patient::class, 'Pasien'], 'professions' => [Profession::class, 'Profesi']];
    public function index(Request $r, string $type)
    {
        abort_unless(isset($this->map[$type]), 404);
        [$class, $label] = $this->map[$type];
        $query = $class::query();
        if ($type === 'patients' && $r->filled('q')) {
            $q = trim((string)$r->query('q'));
            $query->where(function ($x) use ($q) {
                $x->where('name', 'like', '%' . $q . '%')->orWhere('medical_record_no', 'like', '%' . $q . '%');
            });
        }
        $items = $query->orderBy('name')->get();
        $editItem = null;
        if ($r->filled('edit')) $editItem = $class::findOrFail((int)$r->query('edit'));
        $showAdd = $type !== 'patients' && $r->boolean('add');
        $professions = $type === 'teams' ? Profession::where('active', true)->orderBy('name')->get() : collect();
        return view('admin.masters.index', compact('items', 'type', 'label', 'editItem', 'showAdd', 'professions'))->with('q', $r->query('q', ''));
    }
    public function store(Request $r, string $type)
    {
        abort_unless(isset($this->map[$type]), 404);
        [$class] = $this->map[$type];
        $data = $this->validateType($r, $type);
        $class::create($data);
        return back()->with('success', 'Data berhasil ditambahkan.');
    }
    public function update(Request $r, string $type, $item)
    {
        abort_unless(isset($this->map[$type]), 404);
        [$class] = $this->map[$type];
        $model = $class::findOrFail($item);
        $model->update($this->validateType($r, $type));
        return back()->with('success', 'Data berhasil diperbarui.');
    }
    public function destroy(string $type, $item)
    {
        abort_unless(isset($this->map[$type]), 404);
        [$class] = $this->map[$type];
        $class::findOrFail($item)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }
    private function validateType(Request $r, string $type): array
    {
        if ($type === 'patients') {
            $d = $r->validate(['name' => 'required|string|max:255', 'medical_record_no' => 'nullable|string|max:255', 'gender' => 'required|in:P,L', 'birth_date' => 'nullable|date', 'marital_status' => 'nullable|in:Menikah,Belum Menikah']);
            $d['birth_date'] = $d['birth_date'] ?? null;
            $d['marital_status'] = $d['marital_status'] ?? 'Belum Menikah';
            return $d;
        }
        if ($type === 'teams') {
            $d = $r->validate(['name' => 'required|string|max:255', 'profession_id' => 'required|exists:professions,id', 'active' => 'nullable|boolean']);
            $p = Profession::findOrFail($d['profession_id']);
            $d['role'] = $p->name;
            if ($r->has('active')) $d['active'] = $r->boolean('active');
            return $d;
        }
        if ($type === 'professions') {
            $unique = 'unique:professions,name';
            if ($r->route('item')) $unique .= ', ' . (int)$r->route('item');
            $d = $r->validate(['name' => 'required|string|max:100|' . $unique, 'active' => 'nullable|boolean']);
            if ($r->has('active')) $d['active'] = $r->boolean('active');
            return $d;
        }
        $d = $r->validate(['name' => 'required|string|max:255', 'license_no' => 'nullable|string|max:255', 'specialty' => 'nullable|string|max:255', 'active' => 'nullable|boolean']);
        if ($r->has('active')) $d['active'] = $r->boolean('active');
        return $d;
    }
}

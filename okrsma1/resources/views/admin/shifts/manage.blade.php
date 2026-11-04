@extends('layouts.admin')
@section('title', 'Manage Shift')

@push('styles')
<style>
    .shift-color-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 2px rgba(0, 0, 0, .1);
    }
    .shift-badge {
        font-weight: 700;
        letter-spacing: .5px;
        padding: .4rem .75rem;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h4 class="mb-1">Manage Master Shift</h4>
        <small class="text-muted">
            Kelola daftar shift operasional, jam kerja, warna indikator, dan status aktif shift.
        </small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-outline-primary" href="{{ route('admin.shifts.index') }}">
            <i class="bi bi-calendar-event me-1"></i> Jadwal Harian Petugas
        </a>
        <button class="btn btn-rs" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAddShift" aria-expanded="{{ request()->boolean('add') ? 'true' : 'false' }}">
            <i class="bi bi-plus-lg me-1"></i> Tambah Shift Baru
        </button>
    </div>
</div>

@if($editShift)
<div class="card border border-warning shadow-sm mb-4">
    <div class="card-header bg-warning bg-opacity-10 border-warning text-dark py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Shift: {{ $editShift->name }}</h5>
        <a href="{{ route('admin.shifts.manage') }}" class="btn btn-sm btn-outline-secondary">Tutup Form Edit</a>
    </div>
    <form method="POST" action="{{ route('admin.shifts.update', $editShift->id) }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nama Shift <span class="text-danger">*</span></label>
                    <input class="form-control" name="name" value="{{ old('name', $editShift->name) }}" placeholder="Contoh: PAGI, MIDEL, SORE" required autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" name="start_time" value="{{ old('start_time', substr($editShift->start_time, 0, 5)) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" name="end_time" value="{{ old('end_time', substr($editShift->end_time, 0, 5)) }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Warna Label</label>
                    <input type="color" class="form-control form-control-color w-100" name="color" value="{{ old('color', $editShift->color ?: '#1b7a5a') }}" title="Pilih warna untuk shift ini">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status Shift</label>
                    <select class="form-select" name="active">
                        <option value="1" @selected(old('active', $editShift->active ? 1 : 0) == 1)>Aktif (Tampil di Display & Jadwal)</option>
                        <option value="0" @selected(old('active', $editShift->active ? 1 : 0) == 0)>Nonaktif (Disembunyikan)</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Catatan Standar / Keterangan <span class="text-muted small">(opsional)</span></label>
                    <input class="form-control" name="note" value="{{ old('note', $editShift->note) }}" placeholder="Catatan default untuk shift ini...">
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex gap-2">
            <button type="submit" class="btn btn-rs"><i class="bi bi-check-circle me-1"></i> Simpan Perubahan</button>
            <a href="{{ route('admin.shifts.manage') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endif

<div class="collapse {{ request()->boolean('add') && !$editShift ? 'show' : '' }} mb-4" id="collapseAddShift">
    <div class="card border border-success shadow-sm">
        <div class="card-header bg-success bg-opacity-10 border-success text-dark py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2 text-success"></i>Tambah Master Shift Baru</h5>
        </div>
        <form method="POST" action="{{ route('admin.shifts.store') }}">
            @csrf
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nama Shift <span class="text-danger">*</span></label>
                        <input class="form-control" name="name" value="{{ old('name') }}" placeholder="Contoh: PAGI, MIDEL, SORE, MALAM" required autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" name="start_time" value="{{ old('start_time') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" name="end_time" value="{{ old('end_time') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Warna Label</label>
                        <input type="color" class="form-control form-control-color w-100" name="color" value="{{ old('color', '#1b7a5a') }}" title="Pilih warna untuk shift ini">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status Shift</label>
                        <select class="form-select" name="active">
                            <option value="1">Aktif (Tampil di Display & Jadwal)</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Catatan Standar / Keterangan <span class="text-muted small">(opsional)</span></label>
                        <input class="form-control" name="note" value="{{ old('note') }}" placeholder="Catatan default shift ini (cth: ONCALL STANDBY jika sore kurang tenaga)...">
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <button type="submit" class="btn btn-rs"><i class="bi bi-check-circle me-1"></i> Simpan Shift</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#collapseAddShift">Batal</button>
            </div>
        </form>
    </div>
</div>

<div class="card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold"><i class="bi bi-list-columns-reverse me-2 text-success"></i>Daftar Master Shift Operasional</h5>
        <span class="badge rounded-pill bg-light text-dark border px-3 py-2">{{ $shifts->count() }} Shift Terdaftar</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:50px" class="text-center">No</th>
                    <th style="width:70px" class="text-center">Warna</th>
                    <th>Nama Shift</th>
                    <th>Jam Kerja (Operasional)</th>
                    <th>Petugas Hari Ini</th>
                    <th>Catatan Standar</th>
                    <th style="width:130px" class="text-center">Status</th>
                    <th style="width:160px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shifts as $s)
                <tr>
                    <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                    <td class="text-center">
                        <span class="shift-color-dot" style="background-color: {{ $s->color ?: '#1b7a5a' }}"></span>
                    </td>
                    <td>
                        <strong class="fs-6" style="color: {{ $s->color ?: '#1b7a5a' }}">{{ $s->name }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border px-2 py-1">
                            <i class="bi bi-clock me-1 text-success"></i>{{ substr($s->start_time, 0, 5) }} - {{ substr($s->end_time, 0, 5) }} WIB
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-light text-muted border">
                            {{ $s->assignments_count ?? 0 }} petugas terjadwal
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">{{ $s->note ?: '-' }}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ ($s->active ?? true) ? 'bg-success' : 'bg-secondary' }}">
                            {{ ($s->active ?? true) ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="text-center text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.shifts.manage', ['edit' => $s->id]) }}">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <form class="d-inline" method="POST" action="{{ route('admin.shifts.destroy', $s->id) }}" onsubmit="return confirm('Hapus shift {{ $s->name }} beserta seluruh riwayat penugasannya?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>Belum ada data shift yang ditambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

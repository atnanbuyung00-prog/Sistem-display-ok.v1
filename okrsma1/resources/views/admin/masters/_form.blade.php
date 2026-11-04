@if($type==='professions')
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">Nama Profesi <span class="text-danger">*</span></label>
        <input class="form-control" name="name" value="{{ old('name',$item?->name) }}" placeholder="Contoh: Perawat, Bidan, Penata Anestesi" required autocomplete="off">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Status</label>
        <select class="form-select" name="active">
            <option value="1" @selected(old('active', $item ? ($item->active ? 1 : 0) : 1) == 1)>Aktif</option>
            <option value="0" @selected(old('active', $item ? ($item->active ? 1 : 0) : 1) == 0)>Nonaktif</option>
        </select>
    </div>
</div>
@elseif($type==='teams')
<div class="row g-3">
    <div class="col-md-5">
        <label class="form-label fw-semibold">Nama Anggota <span class="text-danger">*</span></label>
        <input class="form-control" name="name" value="{{ old('name',$item?->name) }}" placeholder="Nama lengkap petugas" required autocomplete="off">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Profesi <span class="text-danger">*</span></label>
        <select class="form-select" name="profession_id" required>
            <option value="">-- Pilih Profesi --</option>
            @foreach($professions as $p)
                <option value="{{ $p->id }}" @selected(old('profession_id',$item?->profession_id)==$p->id)>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Status</label>
        <select class="form-select" name="active">
            <option value="1" @selected(old('active', $item ? ($item->active ? 1 : 0) : 1) == 1)>Aktif</option>
            <option value="0" @selected(old('active', $item ? ($item->active ? 1 : 0) : 1) == 0)>Nonaktif</option>
        </select>
    </div>
</div>
@elseif($type==='patients')
<div class="mb-3">
    <label class="form-label fw-semibold">Nama Pasien <span class="text-danger">*</span></label>
    <input class="form-control" name="name" value="{{ old('name',$item?->name) }}" required autocomplete="off">
</div>
<div class="row g-3">
    <div class="col-md-5">
        <label class="form-label fw-semibold">No. RM</label>
        <input class="form-control" name="medical_record_no" value="{{ old('medical_record_no',$item?->medical_record_no) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Jenis Kelamin</label>
        <select class="form-select" name="gender" required>
            <option value="P" @selected(old('gender',$item?->gender)==='P')>Perempuan</option>
            <option value="L" @selected(old('gender',$item?->gender)==='L')>Laki-laki</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Tanggal Lahir <span class="text-muted small">(opsional)</span></label>
        <input type="date" class="form-control" name="birth_date" value="{{ old('birth_date',$item?->birth_date?->format('Y-m-d')) }}">
    </div>
</div>
@else
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
        <input class="form-control" name="name" value="{{ old('name',$item?->name) }}" required autocomplete="off">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">No. STR/SIP</label>
        <input class="form-control" name="license_no" value="{{ old('license_no',$item?->license_no) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Status</label>
        <select class="form-select" name="active">
            <option value="1" @selected(old('active', $item ? ($item->active ? 1 : 0) : 1) == 1)>Aktif</option>
            <option value="0" @selected(old('active', $item ? ($item->active ? 1 : 0) : 1) == 0)>Nonaktif</option>
        </select>
    </div>
</div>
<div>
    <label class="form-label fw-semibold">Spesialisasi</label>
    <input class="form-control" name="specialty" value="{{ old('specialty',$item?->specialty) }}">
</div>
@endif
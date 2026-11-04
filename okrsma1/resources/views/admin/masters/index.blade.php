@extends('layouts.admin')
@section('title','Data '.$label)
@push('styles')<style>
    .patient-search {
        max-width: 620px
    }

    .patient-table {
        font-size: .9rem
    }

    .patient-table th,
    .patient-table td {
        padding: .55rem .65rem
    }
</style>@endpush
@section('content')
<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-0">Data {{ $label }}</h4>
            <small class="text-muted">
                @if($type==='patients')
                    Data pasien otomatis bertambah dari form Jadwal Baru.
                @elseif($type==='professions')
                    Master data profesi untuk anggota tim bedah operasi (cth: Perawat, Bidan, Penata Anestesi).
                @elseif($type==='teams')
                    Master data anggota tim bedah yang bertugas pada jadwal operasi.
                @else
                    Master data yang digunakan pada jadwal operasi.
                @endif
            </small>
        </div>
        <div class="d-flex gap-2">
            @if($type==='professions')
                <a class="btn btn-outline-secondary" href="{{ route('admin.masters.index','teams') }}"><i class="bi bi-people me-1"></i> Data Tim Bedah</a>
                <a class="btn btn-rs" href="{{ route('admin.masters.index',$type) }}?add=1"><i class="bi bi-plus-lg me-1"></i> Tambah Profesi</a>
            @elseif($type==='teams')
                <a class="btn btn-outline-primary" href="{{ route('admin.masters.index','professions') }}"><i class="bi bi-briefcase me-1"></i> Kelola Profesi</a>
                <a class="btn btn-rs" href="{{ route('admin.masters.index',$type) }}?add=1"><i class="bi bi-plus-lg me-1"></i> Tambah Anggota</a>
            @elseif($type!=='patients')
                <a class="btn btn-rs" href="{{ route('admin.masters.index',$type) }}?add=1"><i class="bi bi-plus-lg me-1"></i> Tambah {{ $label }}</a>
            @endif
        </div>
    </div>
    @if($type==='patients')<form method="GET" class="d-flex gap-2 mb-3 patient-search"><input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Cari nama pasien atau No. RM..."><button class="btn btn-rs"><i class="bi bi-search me-1"></i>Cari</button>@if(($q??'')!=='')<a class="btn btn-outline-secondary" href="{{ route('admin.masters.index','patients') }}">Reset</a>@endif</form>@endif
    @if($showAdd && $type!=='patients')<div class="card border border-primary mb-4">
        <div class="card-header bg-primary text-white"><strong>Tambah {{ $label }}</strong></div>
        <form method="POST" action="{{ route('admin.masters.store',$type) }}">@csrf<div class="card-body">@include('admin.masters._form',['item'=>null,'type'=>$type])</div>
            <div class="card-footer bg-white d-flex gap-2"><button class="btn btn-rs" type="submit">Simpan</button><a class="btn btn-outline-secondary" href="{{ route('admin.masters.index',$type) }}">Batal</a></div>
        </form>
    </div>@endif
    @if($editItem)<div class="card border border-warning mb-4">
        <div class="card-header bg-warning"><strong>Edit {{ $label }}</strong></div>
        <form method="POST" action="{{ route('admin.masters.update',[$type,$editItem->id]) }}">@csrf @method('PUT')<div class="card-body">@include('admin.masters._form',['item'=>$editItem,'type'=>$type])</div>
            <div class="card-footer bg-white d-flex gap-2"><button class="btn btn-primary" type="submit">Simpan Perubahan</button><a class="btn btn-outline-secondary" href="{{ route('admin.masters.index',$type) }}">Batal</a></div>
        </form>
    </div>@endif
    <div class="table-responsive">
        <table class="table table-hover align-middle patient-table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">No</th>
                    @if($type==='patients')
                        <th>Nama Pasien</th>
                        <th>No. RM</th>
                        <th>Umur</th>
                        <th>JK</th>
                    @elseif($type==='professions')
                        <th>Nama Profesi</th>
                        <th style="width:130px" class="text-center">Status</th>
                    @elseif($type==='teams')
                        <th>Nama Anggota Tim</th>
                        <th>Profesi</th>
                        <th style="width:130px" class="text-center">Status</th>
                    @else
                        <th>Nama</th>
                        <th>Info / Spesialisasi</th>
                        <th style="width:130px" class="text-center">Status</th>
                    @endif
                    <th style="width:150px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $x)
                <tr>
                    <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                    @if($type==='patients')
                        <td class="fw-semibold">{{ $x->name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $x->medical_record_no ?: '-' }}</span></td>
                        <td>{{ $x->age ?: '-' }}</td>
                        <td>{{ $x->gender==='P'?'Perempuan':'Laki-laki' }}</td>
                    @elseif($type==='professions')
                        <td class="fw-semibold">{{ $x->name }}</td>
                        <td class="text-center"><span class="badge {{ ($x->active ?? true)?'bg-success':'bg-secondary' }}">{{ ($x->active ?? true)?'Aktif':'Nonaktif' }}</span></td>
                    @elseif($type==='teams')
                        <td class="fw-semibold">{{ $x->name }}</td>
                        <td><span class="badge bg-light text-dark border"><i class="bi bi-briefcase me-1 text-success"></i>{{ $x->profession?->name ?? $x->role ?? 'Perawat' }}</span></td>
                        <td class="text-center"><span class="badge {{ ($x->active ?? true)?'bg-success':'bg-secondary' }}">{{ ($x->active ?? true)?'Aktif':'Nonaktif' }}</span></td>
                    @else
                        <td class="fw-semibold">{{ $x->name }}</td>
                        <td>{{ $x->license_no ?: ($x->specialty ?: '-') }}</td>
                        <td class="text-center"><span class="badge {{ ($x->active ?? true)?'bg-success':'bg-secondary' }}">{{ ($x->active ?? true)?'Aktif':'Nonaktif' }}</span></td>
                    @endif
                    <td class="text-center text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.masters.index',[$type]) }}?edit={{ $x->id }}"><i class="bi bi-pencil me-1"></i>Edit</a>
                        <form class="d-inline" method="POST" action="{{ route('admin.masters.destroy',[$type,$x->id]) }}" onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i>Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>Belum ada data {{ strtolower($label) }}.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
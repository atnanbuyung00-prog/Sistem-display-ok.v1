@extends('layouts.admin')
@section('title','Pengaturan Salam')
@section('content')
<div class="row g-4">
    <div class="col-xl-4">
        <div class="card p-4">
            <h4>Tambah Salam</h4>
            <p class="text-muted small">Salam akan muncul sebagai popup ketika pengguna masuk, selama waktu aktifnya sesuai.</p>
            <form method="POST" action="{{ route('admin.greetings.store') }}">@csrf
                <div class="mb-3"><label class="form-label">Judul</label><input name="title" class="form-control" required placeholder="Selamat Pagi"></div>
                <div class="mb-3"><label class="form-label">Isi Salam</label><textarea name="message" class="form-control" rows="3" required placeholder="Selamat bertugas, semoga pelayanan hari ini lancar."></textarea></div>
                <div class="row g-2">
                    <div class="col-6"><label class="form-label">Mulai</label><input type="time" name="start_time" class="form-control" value="07:00" required></div>
                    <div class="col-6"><label class="form-label">Selesai</label><input type="time" name="end_time" class="form-control" value="11:00" required></div>
                </div>
                <div class="form-check mt-3"><input class="form-check-input" type="checkbox" name="enabled" value="1" checked id="enabled"><label class="form-check-label" for="enabled">Aktif</label></div><button class="btn btn-rs mt-3">Tambah Salam</button>
            </form>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card p-4">
            <h4 class="mb-3">Daftar Salam</h4>@forelse($greetings as $g)<form class="border rounded-3 p-3 mb-3" method="POST" action="{{ route('admin.greetings.update',$g) }}">@csrf @method('PUT')<div class="row g-2">
                    <div class="col-md-5"><label class="form-label">Judul</label><input class="form-control" name="title" value="{{ $g->title }}" required></div>
                    <div class="col-md-7"><label class="form-label">Isi Salam</label><input class="form-control" name="message" value="{{ $g->message }}" required></div>
                    <div class="col-4"><label class="form-label">Mulai</label><input class="form-control" type="time" name="start_time" value="{{ substr($g->start_time,0,5) }}" required></div>
                    <div class="col-4"><label class="form-label">Selesai</label><input class="form-control" type="time" name="end_time" value="{{ substr($g->end_time,0,5) }}" required></div>
                    <div class="col-4 d-flex align-items-end">
                        <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="enabled" value="1" id="enabled{{ $g->id }}" @checked($g->enabled)><label class="form-check-label" for="enabled{{ $g->id }}">Aktif</label></div>
                    </div>
                    <div class="col-12 d-flex gap-2"><button class="btn btn-outline-primary btn-sm">Simpan Perubahan</button></form>
            <form method="POST" action="{{ route('admin.greetings.destroy',$g) }}" onsubmit="return confirm('Hapus salam ini?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Hapus</button></form>
        </div>
    </div>@empty<div class="text-muted">Belum ada salam yang diatur.</div>@endforelse
</div>
</div>
</div>
@endsection
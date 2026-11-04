@extends('layouts.admin')
@section('title','Profil Saya')
@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center overflow-hidden" style="width:84px;height:84px">@if($user->profile_photo && file_exists(public_path($user->profile_photo)))<img src="{{ asset($user->profile_photo) }}?v={{ @filemtime(public_path($user->profile_photo)) }}" alt="Foto profil" style="width:100%;height:100%;object-fit:cover">@else<i class="bi bi-person-circle fs-1 text-secondary"></i>@endif</div>
                <div>
                    <h4 class="mb-1">Profil Saya</h4>
                    <div class="text-muted">Ubah nama tampilan, username, kata sandi, dan foto profil.</div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">@csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nama Tampilan</label><input class="form-control" name="name" value="{{ old('name',$user->name) }}" required></div>
                    <div class="col-md-6"><label class="form-label">Username</label><input class="form-control" name="username" value="{{ old('username',$user->username) }}" required></div>
                    <div class="col-12"><label class="form-label">Foto Profil</label><input class="form-control" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" onchange="previewProfilePhoto(event)">
                        <div class="mt-2" id="photoPreviewWrap" style="display:none"><img id="photoPreview" alt="Pratinjau foto" style="width:76px;height:76px;object-fit:cover;border-radius:50%;border:1px solid #dee2e6"></div>
                        <div class="form-text">Format JPG, PNG, WEBP. Maksimal 2 MB.</div>
                    </div>
                    <div class="col-12">
                        <hr>
                        <h5>Keamanan Akun</h5>
                        <p class="small text-muted">Masukkan password saat ini untuk menyimpan perubahan profil.</p>
                    </div>
                    <div class="col-md-6"><label class="form-label">Password Saat Ini</label><input class="form-control" type="password" name="current_password" required autocomplete="current-password"></div>
                    <div class="col-md-6"><label class="form-label">Password Baru (opsional)</label><input class="form-control" type="password" name="password" autocomplete="new-password">
                        <div class="form-text">Kosongkan jika tidak ingin mengganti.</div>
                    </div>
                    <div class="col-md-6"><label class="form-label">Konfirmasi Password Baru</label><input class="form-control" type="password" name="password_confirmation" autocomplete="new-password"></div>
                </div>
                <div class="mt-4"><button class="btn btn-rs"><i class="bi bi-save me-1"></i>Simpan Profil</button></div>
            </form>
        </div>
    </div>
</div>
@push('scripts')<script>
    function previewProfilePhoto(e) {
        const f = e.target.files && e.target.files[0];
        if (!f) return;
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(f.type)) {
            alert('Pilih foto JPG, PNG, atau WEBP.');
            e.target.value = '';
            return;
        }
        if (f.size > 2 * 1024 * 1024) {
            alert('Ukuran foto maksimal 2 MB.');
            e.target.value = '';
            return;
        }
        const img = document.getElementById('photoPreview');
        img.src = URL.createObjectURL(f);
        document.getElementById('photoPreviewWrap').style.display = 'block';
    }
</script>@endpush
@endsection
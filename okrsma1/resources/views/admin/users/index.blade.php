@extends('layouts.admin')

@section('title', 'User & Hak Akses')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-1">User & Hak Akses</h4>
            <small class="text-muted">Buat user baru dan tentukan menu yang boleh dibuka atau dikelola.</small>
        </div>
        <button class="btn btn-rs" data-bs-toggle="modal" data-bs-target="#addUser">
            <i class="bi bi-person-plus"></i> Tambah User
        </button>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <b>Data belum tersimpan.</b>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Hak Akses</th>
                    <th style="width:180px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                <tr>
                    <td class="fw-semibold">{{ $u->name }}</td>
                    <td><code>{{ $u->username }}</code></td>
                    <td>
                        @if ($u->username === 'admin')
                        <span class="badge bg-dark">Semua akses</span>
                        @else
                        @forelse ($u->permissions ?? [] as $permission)
                        <span class="badge bg-primary me-1 mb-1">{{ $permissions[$permission] ?? $permission }}</span>
                        @empty
                        <span class="badge bg-secondary">Belum ada akses</span>
                        @endforelse
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUser{{ $u->id }}">
                            Edit Akses
                        </button>
                        @if ($u->username !== 'admin')
                        <form class="d-inline" method="POST" action="{{ route('admin.users.destroy', $u) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus user ini?')">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Belum ada user.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach ($users as $u)
<div class="modal fade" id="editUser{{ $u->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="{{ route('admin.users.update', $u) }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit User — {{ $u->username }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama</label>
                        <input class="form-control" name="name" value="{{ old('name', $u->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input class="form-control" name="username" value="{{ old('username', $u->username) }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Password baru <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                        <input class="form-control" name="password" type="password" minlength="4" autocomplete="new-password">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Hak Akses</label>
                        <div class="row g-2">
                            @foreach ($permissions as $key => $text)
                            <div class="col-md-6">
                                <label class="border rounded p-2 d-flex gap-2 align-items-start h-100">
                                    <input class="form-check-input mt-1" type="checkbox" name="permissions[]" value="{{ $key }}" @checked(in_array($key, $u->permissions ?? [], true))>
                                    <span><b>{{ $text }}</b><small class="d-block text-muted">{{ $key }}</small></span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-rs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<div class="modal fade" id="addUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Nama</label><input class="form-control" name="name" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Password</label><input class="form-control" name="password" type="password" minlength="4" required autocomplete="new-password"></div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Hak Akses</label>
                        <div class="row g-2">
                            @foreach ($permissions as $key => $text)
                            <div class="col-md-6">
                                <label class="border rounded p-2 d-flex gap-2 align-items-start h-100">
                                    <input class="form-check-input mt-1" type="checkbox" name="permissions[]" value="{{ $key }}">
                                    <span><b>{{ $text }}</b><small class="d-block text-muted">{{ $key }}</small></span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-rs">Simpan User</button>
            </div>
        </form>
    </div>
</div>
@endsection
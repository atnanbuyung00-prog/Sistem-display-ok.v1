<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public const PERMISSIONS = [
        'dashboard.view' => 'Dashboard',
        'schedule.view' => 'Lihat jadwal operasi',
        'schedule.manage' => 'Tambah / ubah / hapus jadwal operasi',
        'schedule.export' => 'Download Excel jadwal operasi',
        'master.manage' => 'Kelola data master',
        'shift.manage' => 'Kelola shift & petugas',
        'shift.export' => 'Download Excel jadwal shift',
        'user.manage' => 'Kelola user & akses',
        'display.view' => 'Buka Display TV',
    ];

    public function index()
    {
        return view('admin.users.index', [
            'users' => User::orderBy('name')->get(),
            'permissions' => self::PERMISSIONS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateUser($request, true);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'permissions' => $data['permissions'] ?? [],
        ]);

        return back()->with('success', 'User berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validateUser($request, false, $user);

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->permissions = $data['permissions'] ?? [];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'User diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->username === 'admin', 403, 'Admin utama tidak boleh dihapus.');

        $user->delete();

        return back()->with('success', 'User dihapus.');
    }

    private function validateUser(Request $request, bool $passwordRequired, ?User $user = null): array
    {
        $uniqueUsername = 'unique:users,username';

        if ($user) {
            $uniqueUsername .= ',' . $user->id;
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', $uniqueUsername],
            'password' => $passwordRequired ? ['required', 'string', 'min:4'] : ['nullable', 'string', 'min:4'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['in:' . implode(',', array_keys(self::PERMISSIONS))],
        ];

        return $request->validate($rules);
    }
}

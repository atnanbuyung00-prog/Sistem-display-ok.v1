<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($user->id)],
            'current_password' => 'required|current_password',
            'password' => 'nullable|string|min:4|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user->name = $data['name'];
        $user->username = $data['username'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($request->hasFile('photo')) {
            $directory = public_path('uploads/profiles');
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }
            $file = $request->file('photo');
            $extension = strtolower($file->extension() ?: 'jpg');
            $filename = 'profile_' . $user->id . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
            $file->move($directory, $filename);
            $user->profile_photo = 'uploads/profiles/' . $filename;
        }

        $user->save();
        return redirect()->route('admin.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}

<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return $this->redirectFor(Auth::user());
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $remember = $request->has('remember') ? $request->boolean('remember') : true;

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return $this->redirectFor($request->user());
        }

        return back()->withErrors(['username' => 'Username atau password salah.'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectFor($user)
    {
        $targets = [
            ['dashboard.view', 'admin.dashboard'],
            ['schedule.view', 'admin.schedules.index'],
            ['shift.manage', 'admin.shifts.index'],
            ['master.manage', 'admin.masters.index', ['patients']],
            ['user.manage', 'admin.users.index'],
            ['display.view', 'admin.display'],
        ];

        foreach ($targets as $target) {
            if ($user->canAccess($target[0])) {
                return isset($target[2]) ? redirect()->route($target[1], $target[2]) : redirect()->route($target[1]);
            }
        }

        Auth::logout();
        return redirect()->route('login')->withErrors(['username' => 'User belum memiliki hak akses. Hubungi administrator.']);
    }
}

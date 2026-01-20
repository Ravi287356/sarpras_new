<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Kalau sudah login, langsung arahkan ke dashboard sesuai role
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('username', 'password');
        $remember = $request->boolean('remember'); // checkbox

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // aman kalau relasi role null
            $role = auth()->user()?->role?->nama;

            return match ($role) {
                'admin' => redirect()->route('admin.dashboard'),
                'operator' => redirect()->route('operator.dashboard'),
                'user' => redirect()->route('user.dashboard'),
                default => $this->forceLogoutWithError($request, 'Role tidak dikenali'),
            };
        }

        return back()
            ->withErrors(['username' => 'Username atau password salah'])
            ->onlyInput('username');
    }

    private function forceLogoutWithError(Request $request, string $message)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')->withErrors(['username' => $message]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }
}

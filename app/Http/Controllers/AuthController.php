<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
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
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // ✅ log login
            $this->logActivity(
                aksi: 'LOGIN',
                deskripsi: $this->whoPrefixed('Login')
            );

            $role = auth()->user()?->role?->nama;

            return match ($role) {
                'admin'    => redirect()->route('admin.dashboard'),
                'operator' => redirect()->route('operator.dashboard'),
                'user'     => redirect()->route('user.dashboard'),
                default    => $this->forceLogoutWithError($request, 'Role tidak dikenali'),
            };
        }

        // ✅ Cek apakah username ada untuk membedakan alasan gagal
        $userExists = \App\Models\User::where('username', $request->username)->exists();
        $failReason = $userExists ? 'Password salah' : 'Username tidak ditemukan';

        // ✅ catat login gagal
        $this->logActivity(
            aksi: 'LOGIN_GAGAL',
            deskripsi: "Login gagal ({$failReason}) untuk username: " . ($request->username ?? '-')
        );

        return back()
            ->withErrors(['username' => 'Username atau password salah'])
            ->onlyInput('username');
    }

    private function forceLogoutWithError(Request $request, string $message)
    {
        $this->logActivity(
            aksi: 'LOGIN_GAGAL_ROLE',
            deskripsi: $message . ' | ' . $this->whoPrefixed('Login')
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')->withErrors(['username' => $message]);
    }

    public function logout(Request $request)
    {
        // ✅ log logout sebelum Auth::logout()
        $this->logActivity(
            aksi: 'LOGOUT',
            deskripsi: $this->whoPrefixed('Logout')
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }

    /**
     * Format "siapa dan role"
     */
    private function whoText(string $prefix): string
    {
        return $this->whoPrefixed($prefix);
    }

    /**
     * ✅ Sesuai struktur tabel kamu: user_id, aksi, deskripsi, timestamp
     */
    private function writeLog(string $aksi, ?string $deskripsi = null): void
    {
        $this->logActivity($aksi, $deskripsi);
    }
}

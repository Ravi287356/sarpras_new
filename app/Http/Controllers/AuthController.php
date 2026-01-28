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
            $this->writeLog(
                aksi: 'LOGIN',
                deskripsi: $this->whoText('Login')
            );

            $role = auth()->user()?->role?->nama;

            return match ($role) {
                'admin'    => redirect()->route('admin.dashboard'),
                'operator' => redirect()->route('operator.dashboard'),
                'user'     => redirect()->route('user.dashboard'),
                default    => $this->forceLogoutWithError($request, 'Role tidak dikenali'),
            };
        }

        // ✅ opsional: catat login gagal (tanpa user_id karena belum login)
        $this->writeLog(
            aksi: 'LOGIN_GAGAL',
            deskripsi: 'Login gagal untuk username: ' . ($request->username ?? '-')
        );

        return back()
            ->withErrors(['username' => 'Username atau password salah'])
            ->onlyInput('username');
    }

    private function forceLogoutWithError(Request $request, string $message)
    {
        $this->writeLog(
            aksi: 'LOGIN_GAGAL_ROLE',
            deskripsi: $message . ' | ' . $this->whoText('Login')
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')->withErrors(['username' => $message]);
    }

    public function logout(Request $request)
    {
        // ✅ log logout sebelum Auth::logout()
        $this->writeLog(
            aksi: 'LOGOUT',
            deskripsi: $this->whoText('Logout')
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
        $username = auth()->user()?->username ?? '-';
        $role     = auth()->user()?->role?->nama ?? '-';
        return "{$prefix} oleh {$username} ({$role})";
    }

    /**
     * ✅ Sesuai struktur tabel kamu: user_id, aksi, deskripsi, timestamp
     */
    private function writeLog(string $aksi, ?string $deskripsi = null): void
    {
        try {
            ActivityLog::create([
                'user_id'   => auth()->check() ? auth()->id() : null,
                'aksi'      => $aksi,
                'deskripsi' => $deskripsi,
                // timestamp otomatis oleh DB (CURRENT_TIMESTAMP)
            ]);
        } catch (\Throwable $e) {
            // jangan crash aplikasi
        }
    }
}

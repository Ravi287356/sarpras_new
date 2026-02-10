<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('pages.profile.edit', [
            'title' => 'Profil',
            'user'  => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'password_lama' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->username = $request->username;
        $user->email    = $request->email;

        // Jika mengisi password baru, wajib isi password lama dan harus sesuai
        if ($request->filled('password')) {
            if (!$request->filled('password_lama')) {
                return back()->with('error', 'Password lama wajib diisi untuk mengganti password baru.');
            }

            if (!Hash::check($request->password_lama, $user->password)) {
                return back()->with('error', 'Password lama tidak sesuai.');
            }

            $user->password = Hash::make($request->password);
        }

        $user->save();

        // ✅ Log activity
        $this->logActivity(
            aksi: 'PROFIL_UPDATE',
            deskripsi: 'Update profil: ' . $request->username
        );

        return back()->with('success', 'Profil berhasil diperbarui ✅');
    }
}

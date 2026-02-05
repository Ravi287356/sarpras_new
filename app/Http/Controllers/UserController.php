<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // WAJIB with('role') supaya $u->role?->nama muncul
        $users = User::with('role')
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ ini sesuai file kamu: pages/admin/datauser.blade.php
        return view('pages.admin.datauser', [
            'title' => 'Daftar User',
            'users' => $users,
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('nama', 'asc')->get();

        // ✅ ini sesuai file kamu: pages/admin/create.blade.php
        return view('pages.admin.create', [
            'title' => 'Tambah User',
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|max:255|unique:users,email',
            'role_id'  => 'required|exists:roles,id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'role_id'  => $request->role_id,
            'password' => Hash::make($request->password),
        ]);

        // ✅ Log activity
        $this->logActivity(
            aksi: 'USER_BUAT',
            deskripsi: 'Buat user: ' . $request->username . ' (' . $request->email . ')'
        );

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan ✅');
    }

    public function edit($id)
    {
        $user  = User::findOrFail($id);
        $roles = Role::orderBy('nama', 'asc')->get();

        // ✅ ini sesuai file kamu: pages/admin/edit.blade.php
        return view('pages.admin.edit', [
            'title' => 'Edit User',
            'user'  => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'role_id'  => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->username = $request->username;
        $user->email    = $request->email;
        $user->role_id  = $request->role_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // ✅ Log activity
        $this->logActivity(
            aksi: 'USER_UPDATE',
            deskripsi: 'Update user: ' . $request->username . ' (' . $request->email . ')'
        );

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate ✅');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $username = $user->username;

        // ✅ Log activity
        $this->logActivity(
            aksi: 'USER_HAPUS',
            deskripsi: 'Hapus user: ' . $username
        );

        $user->delete();

        return back()->with('success', 'User berhasil dihapus ✅');
    }
}

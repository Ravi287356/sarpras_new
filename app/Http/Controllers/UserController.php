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
        // urutan: paling lama di atas, paling baru di bawah
        $users = User::with('role')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.admin.datauser', [
            'title' => 'Daftar User',
            'users' => $users,
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('nama', 'asc')->get();

        return view('pages.admin.create', [
            'title' => 'Tambah User',
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::orderBy('nama', 'asc')->get();

        return view('pages.admin.edit', [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'min:6', 'confirmed'],
        ]);

        $user->username = $request->username;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy($id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}

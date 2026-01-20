@extends('layouts.app')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-semibold mb-1">Tambah User</h1>
    <p class="text-slate-300 text-sm mb-6">Buat akun baru untuk sistem.</p>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-rose-500/40 bg-rose-500/10 p-4 text-rose-200">
            <b>Terjadi kesalahan:</b>
            <ul class="list-disc pl-5 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-sm text-slate-200">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required
                       class="mt-2 w-full px-4 py-3 rounded-xl bg-slate-950/40 border border-white/10
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
            </div>

            <div>
                <label class="text-sm text-slate-200">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="mt-2 w-full px-4 py-3 rounded-xl bg-slate-950/40 border border-white/10
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
            </div>

            <div>
                <label class="text-sm text-slate-200">Role</label>
                <select name="role_id" required
                        class="mt-2 w-full px-4 py-3 rounded-xl bg-slate-950/40 border border-white/10
                               focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" @selected(old('role_id') == $r->id)>
                            {{ $r->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm text-slate-200">Password</label>
                <input type="password" name="password" required
                       class="mt-2 w-full px-4 py-3 rounded-xl bg-slate-950/40 border border-white/10
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
            </div>

            <div>
                <label class="text-sm text-slate-200">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                       class="mt-2 w-full px-4 py-3 rounded-xl bg-slate-950/40 border border-white/10
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit"
                        class="px-5 py-2 rounded-xl border border-emerald-400/30 bg-emerald-500/10 text-emerald-200 hover:bg-emerald-500/20 transition">
                    Simpan
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2 rounded-xl border border-white/10 hover:bg-white/5 transition">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

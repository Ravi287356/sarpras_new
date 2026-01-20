@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Profil</h1>
            <p class="text-slate-400 text-sm mt-1">Ubah username dan password kamu.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl bg-emerald-500/10 ring-1 ring-emerald-500/30 p-4 text-emerald-200 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl bg-red-500/10 ring-1 ring-red-500/30 p-4 text-red-200 text-sm">
            <b>Gagal:</b>
            <ul class="list-disc list-inside mt-2 space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 p-6">
        <form method="POST"
            action="{{ route($roleName . '.profile.update') }}"
            class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="text-sm text-slate-300">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                    class="mt-2 w-full rounded-xl bg-slate-950/60 ring-1 ring-slate-800 px-4 py-3
                           placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
            </div>
            <div class="mb-4">
            <label class="text-sm text-slate-300">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                class="mt-2 w-full rounded-xl bg-slate-950/60 ring-1 ring-slate-800 px-4 py-3
                    focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-slate-300">Password Baru (opsional)</label>
                    <input type="password" name="password"
                        class="mt-2 w-full rounded-xl bg-slate-950/60 ring-1 ring-slate-800 px-4 py-3
                               placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
                    <p class="text-xs text-slate-500 mt-2">Kosongkan jika tidak ingin ganti password.</p>
                </div>

                <div>
                    <label class="text-sm text-slate-300">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                        class="mt-2 w-full rounded-xl bg-slate-950/60 ring-1 ring-slate-800 px-4 py-3
                               placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
                </div>
            </div>

            <button type="submit"
                class="px-5 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/25
                       text-emerald-200 ring-1 ring-emerald-500/30 transition font-medium">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection

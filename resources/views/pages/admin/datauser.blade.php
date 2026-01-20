@extends('layouts.app')

@section('content')
<div class="max-w-6xl">

    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Daftar User</h1>
            <p class="text-slate-300 text-sm mt-1">Kelola akun user (khusus admin).</p>
        </div>

        <a href="{{ route('admin.users.create') }}"
           class="px-5 py-2 rounded-xl border border-emerald-400/30 bg-emerald-500/10 text-emerald-200 hover:bg-emerald-500/20 transition">
            Tambah User
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-400/30 bg-emerald-500/10 p-4 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-white/10 bg-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-white/5 text-slate-200">
                <tr>
                    <th class="text-left px-6 py-4 w-16">#</th>
                    <th class="text-left px-6 py-4">Username</th>
                    <th class="text-left px-6 py-4">Email</th>
                    <th class="text-left px-6 py-4">Role</th>
                    <th class="text-left px-6 py-4 w-56">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-white/10">
                @foreach ($users as $i => $u)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-6 py-4">{{ $i + 1 }}</td>
                        <td class="px-6 py-4 font-semibold">{{ $u->username }}</td>
                        <td class="px-6 py-4 text-slate-200">{{ $u->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full border border-white/10 bg-slate-950/40">
                                {{ $u->role?->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex gap-2">
                            <a href="{{ route('admin.users.edit', $u->id) }}"
                               class="px-4 py-2 rounded-xl border border-white/10 hover:bg-white/5 transition">
                                Edit
                            </a>

                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2 rounded-xl border border-rose-500/40 text-rose-200 hover:bg-rose-500/10 transition">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @if($users->count() === 0)
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                            Data user masih kosong.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

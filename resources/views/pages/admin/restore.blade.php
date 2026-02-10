@extends('layouts.app')

@section('content')
<div class="max-w-6xl">

    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">User Terhapus</h1>
            <p class="text-slate-300 text-sm mt-1">Daftar user yang telah dihapus dan bisa dipulihkan</p>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="px-5 py-2 rounded-xl border border-slate-400/30 bg-slate-500/10 text-slate-200 hover:bg-slate-500/20 transition">
            Kembali ke Daftar User
        </a>
    </div>


    <div class="rounded-2xl border border-white/10 bg-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-white/5 text-slate-200">
                <tr>
                    <th class="text-left px-6 py-4 w-16">No</th>
                    <th class="text-left px-6 py-4">Username</th>
                    <th class="text-left px-6 py-4">Email</th>
                    <th class="text-left px-6 py-4">Role</th>
                    <th class="text-left px-6 py-4">Dihapus Pada</th>
                    <th class="text-left px-6 py-4 w-40">Aksi</th>
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
                        <td class="px-6 py-4 text-slate-300">
                            {{ $u->deleted_at?->format('d-m-Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.users.restore', $u->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 rounded-xl border border-emerald-500/40 text-emerald-200 hover:bg-emerald-500/10 transition">
                                    Pulihkan
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @if($users->count() === 0)
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                            Tidak ada user yang terhapus.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

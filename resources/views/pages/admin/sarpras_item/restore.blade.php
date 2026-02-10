@extends('layouts.app')

@section('content')
<div class="max-w-7xl">

    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-white">📦 Item Sarpras Terhapus</h1>
            <p class="text-slate-400 text-sm mt-1">Daftar item sarpras yang telah dihapus dan bisa dipulihkan</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.sarpras.index') }}"
               class="px-5 py-2 rounded-xl border border-slate-400/30 bg-slate-500/10 text-slate-200 hover:bg-slate-500/20 transition">
                Kembali ke Daftar Sarpras
            </a>
        </div>
    </div>


    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-950/40 text-slate-200">
                <tr class="text-left">
                    <th class="px-5 py-4 w-16">No</th>
                    <th class="px-5 py-4">Kode Item</th>
                    <th class="px-5 py-4">Sarpras</th>
                    <th class="px-5 py-4">Lokasi</th>
                    <th class="px-5 py-4 text-slate-300">Dihapus Pada</th>
                    <th class="px-5 py-4 w-32 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800">
                @foreach ($items as $i => $u)
                    <tr class="hover:bg-slate-950/30 transition text-slate-200">
                        <td class="px-5 py-4 text-slate-400 font-mono">{{ $i + 1 }}</td>
                        <td class="px-5 py-4 font-mono font-bold text-emerald-400 uppercase tracking-wider">{{ $u->kode }}</td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-white">{{ $u->sarpras?->nama }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-1 rounded-lg bg-slate-800 text-slate-300 text-xs">
                                {{ $u->lokasi?->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-slate-400">
                            {{ $u->deleted_at?->format('d-m-Y H:i') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            <form action="{{ route('admin.sarpras_item.restore', $u->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 rounded-xl border border-emerald-500/40 text-emerald-200 hover:bg-emerald-500/10 transition text-xs font-semibold">
                                    Pulihkan
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @if($items->count() === 0)
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500 italic">
                            Tidak ada item sarpras yang terhapus.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

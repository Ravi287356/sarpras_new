@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <a href="{{ route(auth()->user()->role->nama . '.sarpras.index') }}" class="text-emerald-400 text-sm">← Kembali ke daftar</a>

    <div class="flex items-center justify-between gap-4 mt-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-white">Inventory: {{ $sarpras->nama }}</h1>
            <p class="text-slate-400 text-sm">Daftar unit / item untuk sarpras ini (Total: {{ $sarpras->items->count() }})</p>
        </div>

        @if (auth()->user()->role->nama === 'admin')
            <div class="flex gap-2">
                <a href="{{ route('admin.sarpras.edit', $sarpras->id) }}"
                   class="px-5 py-3 rounded-xl bg-blue-600/20 hover:bg-blue-600/25
                          text-blue-200 ring-1 ring-blue-500/30 transition font-medium">
                    Edit Sarpras
                </a>

                <a href="{{ route('admin.sarpras_item.create', $sarpras->id) }}"
                   class="px-5 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/25
                          text-emerald-200 ring-1 ring-emerald-500/30 transition font-medium">
                    + Tambah Item
                </a>
            </div>
        @endif
    </div>


    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-slate-300 bg-white/5">
                    <tr>
                        <th class="text-left px-5 py-4">No</th>
                        <th class="text-left px-5 py-4">Kode</th>
                        <th class="text-left px-5 py-4">Lokasi</th>
                        <th class="text-left px-5 py-4">Kondisi</th>
                        <th class="text-left px-5 py-4">Status Peminjaman</th>
                        @if (auth()->user()->role->nama === 'admin')
                            <th class="text-left px-5 py-4 w-40">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($sarpras->items as $i => $item)
                        <tr class="text-slate-200">
                            <td class="px-5 py-4">{{ $i+1 }}</td>
                             <td class="px-5 py-4 font-medium">
                                <a href="{{ route('admin.sarpras_item.show', $item->id) }}" class="text-emerald-400 hover:text-emerald-300 transition underline underline-offset-4 decoration-emerald-500/30">
                                    {{ $item->kode }}
                                </a>
                             </td>
                            <td class="px-5 py-4">{{ $item->lokasi?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $item->kondisi?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $item->statusPeminjaman?->nama ?? '-' }}</td>
                            @if (auth()->user()->role->nama === 'admin')
                                <td class="px-5 py-4 flex gap-2">
                                    <a href="{{ route('admin.inspeksi.create', $item->id) }}"
                                       class="px-3 py-2 rounded-lg bg-emerald-500/10 text-emerald-200 ring-1 ring-emerald-500/30 hover:bg-emerald-500/15 transition text-sm">
                                        Inspeksi
                                    </a>

                                    <a href="{{ route('admin.sarpras_item.edit', $item->id) }}"
                                       class="px-3 py-2 rounded-lg ring-1 ring-white/10 hover:bg-white/5 transition text-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.sarpras_item.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-2 rounded-lg bg-red-500/10 text-red-200
                                                       ring-1 ring-red-500/30 hover:bg-red-500/15 transition text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role->nama === 'admin' ? '6' : '5' }}" class="px-5 py-8 text-center text-slate-400">
                                Belum ada item. 
                                @if (auth()->user()->role->nama === 'admin')
                                    <a href="{{ route('admin.sarpras_item.create', $sarpras->id) }}" class="text-emerald-400">Tambah item sekarang</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

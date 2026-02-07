@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <a href="{{ route('admin.sarpras.index') }}" class="text-emerald-400 text-sm">← Kembali ke daftar</a>

    <div class="flex items-center justify-between gap-4 mt-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-white">Inventory: {{ $sarpras->nama }}</h1>
            <p class="text-slate-400 text-sm">Daftar unit / item untuk sarpras ini (Total: {{ $sarpras->items->count() }})</p>
        </div>

        <a href="{{ route('admin.sarpras_item.create', $sarpras->id) }}"
           class="px-5 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/25
                  text-emerald-200 ring-1 ring-emerald-500/30 transition font-medium">
            + Tambah Item
        </a>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl bg-emerald-500/10 ring-1 ring-emerald-500/30 p-4 text-emerald-200 text-sm">
            {{ session('success') }}
        </div>
    @endif

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
                        <th class="text-left px-5 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($sarpras->items as $i => $item)
                        <tr class="text-slate-200">
                            <td class="px-5 py-4">{{ $i+1 }}</td>
                            <td class="px-5 py-4 font-medium">{{ $item->kode }}</td>
                            <td class="px-5 py-4">{{ $item->lokasi?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $item->kondisi?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $item->statusPeminjaman?->nama ?? '-' }}</td>
                            <td class="px-5 py-4 flex gap-2">
                                <a href="{{ route('admin.sarpras_item.edit', $item->id) }}"
                                   class="px-3 py-2 rounded-lg ring-1 ring-white/10 hover:bg-white/5 transition text-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.sarpras_item.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus item ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-2 rounded-lg bg-red-500/10 text-red-200
                                                   ring-1 ring-red-500/30 hover:bg-red-500/15 transition text-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Belum ada item. <a href="{{ route('admin.sarpras_item.create', $sarpras->id) }}" class="text-emerald-400">Tambah item sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

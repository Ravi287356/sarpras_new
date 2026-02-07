@extends('layouts.app')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Sarpras Bisa Dipinjam</h1>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-500/10 text-emerald-200 ring-1 ring-emerald-500/30 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl bg-rose-500/10 text-rose-200 ring-1 ring-rose-500/30 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-slate-200">
                    <tr>
                        <th class="px-5 py-4 text-left w-14">No</th>
                        <th class="px-5 py-4 text-left">Kode</th>
                        <th class="px-5 py-4 text-left">Nama</th>
                        <th class="px-5 py-4 text-left">Kategori</th>
                        <th class="px-5 py-4 text-left">Lokasi</th>
                        <th class="px-5 py-4 text-left w-24">Stok</th>
                        <th class="px-5 py-4 text-left w-28">Kondisi</th>
                        <th class="px-5 py-4 text-left w-32">Status</th>
                        <th class="px-5 py-4 text-left w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($items as $i => $row)
                        <tr class="text-slate-100">
                            <td class="px-5 py-4">{{ $i + 1 }}</td>
                            <td class="px-5 py-4">{{ $row->items->first()?->kode ?? $row->kode }}</td>
                            <td class="px-5 py-4 font-medium">{{ $row->nama }}</td>
                            <td class="px-5 py-4">{{ $row->kategori?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $row->items->first()?->lokasi?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">{{ (int)$row->jumlah_stok }}</td>
                            <td class="px-5 py-4">{{ $row->items->first()?->kondisi?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">
                                @php
                                    $firstItem = $row->items->first();
                                    $status = $firstItem?->getDisplayStatus() ?? 'TERSEDIA';
                                    $color = $firstItem?->getStatusBadgeColor() ?? 'slate';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold
                                    @if($color === 'emerald')
                                        bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-500/30
                                    @elseif($color === 'amber')
                                        bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30
                                    @elseif($color === 'rose')
                                        bg-rose-500/20 text-rose-300 ring-1 ring-rose-500/30
                                    @else
                                        bg-slate-500/20 text-slate-300 ring-1 ring-slate-500/30
                                    @endif">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('user.peminjaman.create', $row->id) }}"
                                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm
                                          bg-emerald-500/10 text-emerald-200 ring-1 ring-emerald-500/30 hover:bg-emerald-500/15 transition">
                                    Ajukan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-slate-400">
                                Tidak ada sarpras yang bisa dipinjam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

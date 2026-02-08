@extends('layouts.app')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Sarpras Tersedia</h1>
    </div>

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-slate-200">
                    <tr>
                        <th class="px-5 py-4 text-left w-14">No</th>

                        <th class="px-5 py-4 text-left">Nama</th>
                        <th class="px-5 py-4 text-left">Kategori</th>
                        <th class="px-5 py-4 text-left">Lokasi</th>
                        <th class="px-5 py-4 text-left w-24">Stok</th>
                        <th class="px-5 py-4 text-left">Kondisi</th>
                        <th class="px-5 py-4 text-left w-32">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($items as $i => $row)
                        @php
                            $availableItem = $row->sample_item;
                            $status = $availableItem?->getDisplayStatus() ?? 'TERSEDIA';
                            $color = $availableItem?->getStatusBadgeColor() ?? 'slate';
                        @endphp
                        <tr class="text-slate-100">
                            <td class="px-5 py-4">{{ $i + 1 }}</td>

                            <td class="px-5 py-4 font-medium">{{ $row->nama }}</td>
                            <td class="px-5 py-4">{{ $row->kategori?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $row->lokasi_saat_ini ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $row->jumlah_stok }}</td>
                            <td class="px-5 py-4">{{ $row->kondisi_saat_ini ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold
                                    @if($color === 'emerald')
                                        bg-emerald-500/20 text-emerald-300 ring-1 ring-emerald-500/30
                                    @elseif($color === 'amber')
                                        bg-amber-500/20 text-amber-300 ring-1 ring-amber-500/30
                                    @elseif($color === 'rose')
                                        bg-rose-500/20 text-rose-300 ring-1 ring-rose-500/30
                                    @elseif($color === 'indigo')
                                        bg-indigo-500/20 text-indigo-300 ring-1 ring-indigo-500/30
                                    @else
                                        bg-slate-500/20 text-slate-300 ring-1 ring-slate-500/30
                                    @endif">
                                    {{ $status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400">
                                Data sarpras belum ada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

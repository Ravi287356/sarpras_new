@extends('layouts.app')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Manajemen Maintenance</h1>
            <p class="text-slate-400 text-sm mt-1">Kelola perbaikan dan pemeliharaan alat.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl bg-emerald-500/10 text-emerald-200 ring-1 ring-emerald-500/30 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-slate-200">
                    <tr>
                        <th class="px-5 py-4 text-left w-14">No</th>
                        <th class="px-5 py-4 text-left">Kode</th>
                        <th class="px-5 py-4 text-left">Nama Sarpras</th>
                        <th class="px-5 py-4 text-left">Lokasi</th>
                        <th class="px-5 py-4 text-left">Kondisi</th>
                        <th class="px-5 py-4 text-left">Status</th>
                        <th class="px-5 py-4 text-center w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 text-slate-100">
                    @forelse($items as $i => $item)
                        @php
                            $status = $item->getDisplayStatus();
                            $color = $item->getStatusBadgeColor();
                        @endphp
                        <tr>
                            <td class="px-5 py-4">{{ $i + 1 }}</td>
                            <td class="px-5 py-4 font-mono text-xs">{{ $item->kode }}</td>
                            <td class="px-5 py-4">
                                <span class="font-medium text-slate-100">{{ $item->sarpras?->nama }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-300">{{ $item->lokasi?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="uppercase text-xs font-semibold px-2 py-0.5 rounded
                                    @if($item->kondisi?->nama === 'Baik') text-emerald-400 bg-emerald-400/10
                                    @elseif($item->kondisi?->nama === 'Rusak Ringan') text-amber-400 bg-amber-400/10
                                    @else text-rose-400 bg-rose-400/10 @endif">
                                    {{ $item->kondisi?->nama }}
                                </span>
                            </td>
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
                            <td class="px-5 py-4 text-center">
                                @if($status === 'MAINTENANCE')
                                    <form action="{{ route('admin.maintenance.finish', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                            class="w-full px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-500/10 text-emerald-200 ring-1 ring-emerald-500/30 hover:bg-emerald-500/20 transition">
                                            Selesai Perbaikan
                                        </button>
                                    </form>
                                @elseif($status === 'TERSEDIA' || $status === 'BUTUH MAINTENANCE' || $status === 'SEDANG MAINTENANCE')
                                    <form action="{{ route('admin.maintenance.start', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                            class="w-full px-4 py-2 rounded-xl text-xs font-semibold bg-rose-500/10 text-rose-200 ring-1 ring-rose-500/30 hover:bg-rose-500/20 transition">
                                            Mulai Maintenance
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-500 italic">Sedang Dipinjam</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400 italic">
                                Tidak ada data sarpras.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

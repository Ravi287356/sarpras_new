@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.asset_health.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-white/10 hover:bg-white/5 transition">
        <i class="fa-solid fa-arrow-left text-slate-400"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-white mb-1">{{ $title }}</h1>
        <p class="text-slate-400 text-sm">Ranking Top 10 alat yang paling sering rusak</p>
    </div>
</div>

<div class="bg-slate-900 border border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-950 text-slate-200 uppercase font-medium border-b border-white/10">
                <tr>
                    <th class="px-6 py-4">Rank</th>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Nama Barang</th>
                    <th class="px-6 py-4 text-center">Jumlah Kerusakan</th>
                    <th class="px-6 py-4">Lokasi Saat Ini</th>
                    <th class="px-6 py-4">Status Saat Ini</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($items as $index => $item)
                <tr class="hover:bg-white/5 transition">
                    <td class="px-6 py-4">
                        @if($index < 3)
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold
                                {{ $index == 0 ? 'bg-yellow-500/20 text-yellow-500 ring-1 ring-yellow-500/50' : '' }}
                                {{ $index == 1 ? 'bg-slate-400/20 text-slate-300 ring-1 ring-slate-400/50' : '' }}
                                {{ $index == 2 ? 'bg-orange-700/20 text-orange-400 ring-1 ring-orange-700/50' : '' }}
                            ">
                                {{ $index + 1 }}
                            </div>
                        @else
                            <span class="pl-3 font-medium text-slate-500">#{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-mono text-slate-300">{{ $item->kode }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-white">{{ $item->sarpras->nama }}</div>
                        <div class="text-xs">{{ $item->sarpras->kategori->nama ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 font-bold">
                            {{ $item->breakdown_count }}x
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $item->lokasi->nama ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @php
                            $status = $item->getDisplayStatus();
                            $color = $item->getStatusBadgeColor();
                        @endphp
                        <span class="px-2 py-1 rounded text-xs font-medium bg-{{ $color }}-500/10 text-{{ $color }}-400 border border-{{ $color }}-500/20">
                            {{ $status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-trophy text-4xl mb-4 text-slate-700"></i>
                        <p>Belum ada data kerusakan yang tercatat.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

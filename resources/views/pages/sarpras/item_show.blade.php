@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.sarpras.items', $item->sarpras_id) }}" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white transition">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Detail Aset: {{ $item->kode }}</h1>
            <p class="text-slate-400 text-sm mt-1">{{ $item->sarpras->nama }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="p-6 rounded-2xl bg-slate-900/40 ring-1 ring-white/10 space-y-4">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Informasi Dasar</h3>
                
                <div class="space-y-3">
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold">Status</span>
                        <div class="mt-1">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold ring-1 
                                bg-{{ $item->getStatusBadgeColor() }}-500/10 text-{{ $item->getStatusBadgeColor() }}-200 ring-{{ $item->getStatusBadgeColor() }}-500/30">
                                {{ $item->getDisplayStatus() }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold">Lokasi</span>
                        <p class="text-slate-200 text-sm">{{ $item->lokasi?->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold">Kondisi Fisik</span>
                        <p class="text-slate-200 text-sm">{{ $item->kondisi?->nama ?? '-' }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-white/5 flex flex-col gap-2">
                    <a href="{{ route('admin.inspeksi.create', $item->id) }}" 
                       class="w-full py-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 text-xs font-bold rounded-xl ring-1 ring-emerald-500/30 transition text-center">
                        Lakukan Inspeksi Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content (History) -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest px-2">Riwayat Inspeksi</h3>
            
            <div class="space-y-4">
                @forelse($inspections as $insp)
                    <div x-data="{ open: false }" class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
                        <button @click="open = !open" class="w-full p-5 flex items-center justify-between hover:bg-white/[0.02] transition">
                            <div class="flex items-center gap-4 text-left">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-slate-400">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div>
                                    <h4 class="text-slate-200 font-medium text-sm">{{ $insp->tanggal_inspeksi->format('d M Y, H:i') }}</h4>
                                    <p class="text-[10px] text-slate-500 italic">Oleh: {{ $insp->user->username }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] bg-white/5 px-2 py-1 rounded text-slate-400">
                                    {{ $insp->results->count() }} item periksa
                                </span>
                                <i class="bi text-slate-500 transition-transform" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                            </div>
                        </button>

                        <div x-show="open" x-collapse class="p-5 border-t border-white/5 bg-black/20">
                            @if($insp->catatan_umum)
                                <div class="mb-5 p-4 rounded-xl bg-blue-500/5 ring-1 ring-blue-500/20 text-xs text-blue-200 italic">
                                    "{{ $insp->catatan_umum }}"
                                </div>
                            @endif

                            <div class="space-y-3">
                                @foreach($insp->results as $res)
                                    <div class="flex items-start justify-between gap-4 p-3 rounded-xl bg-white/5 ring-1 ring-white/5">
                                        <div>
                                            <p class="text-xs text-slate-300 font-medium">{{ $res->checklist->tujuan_periksa }}</p>
                                            @if($res->catatan)
                                                <p class="text-[10px] text-slate-500 mt-1">{{ $res->catatan }}</p>
                                            @endif
                                        </div>
                                        <span class="px-2 py-1 rounded text-[10px] font-bold 
                                            @if($res->status == 'Baik') bg-emerald-500/20 text-emerald-400 
                                            @elseif($res->status == 'Rusak') bg-rose-500/20 text-rose-400 
                                            @else bg-slate-500/20 text-slate-400 @endif uppercase">
                                            {{ $res->status }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center rounded-2xl bg-slate-900/40 border border-dashed border-slate-800">
                        <i class="bi bi-search text-2xl text-slate-700 mb-3 block"></i>
                        <p class="text-slate-500 text-sm italic">Belum ada riwayat inspeksi untuk aset ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

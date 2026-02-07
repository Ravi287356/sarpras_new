@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white mb-1">{{ $title }}</h1>
        <p class="text-slate-400 text-sm">Monitor kondisi aset sarpras secara real-time</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- CARD 1: TOTAL ISSUES --}}
    <div class="bg-slate-900 border border-white/10 rounded-2xl p-6 relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/10 rounded-full blur-xl group-hover:bg-rose-500/20 transition"></div>
        
        <div class="relative">
            <div class="w-10 h-10 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-400 mb-4">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            </div>
            
            <div class="text-3xl font-bold text-white mb-1">{{ $totalIssues }}</div>
            <div class="text-sm text-slate-400">Total Masalah Aset</div>
            
            <div class="mt-4 pt-4 border-t border-white/5 text-xs text-slate-500">
                Laporan kesehatan aset
            </div>
        </div>
    </div>

    {{-- CARD 2: RUSAK --}}
    <a href="{{ route('admin.asset_health.rusak') }}" class="bg-slate-900 border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-rose-500/30 transition">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-500/10 rounded-full blur-xl group-hover:bg-orange-500/20 transition"></div>
        
        <div class="relative">
            <div class="w-10 h-10 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-400 mb-4">
                <i class="fa-solid fa-hammer text-lg"></i>
            </div>
            
            <div class="text-3xl font-bold text-white mb-1">{{ $rusakCount }}</div>
            <div class="text-sm text-slate-400">Asset Health - Rusak</div>
            
            <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                <span class="text-xs text-slate-500">Lihat detail</span>
                <i class="fa-solid fa-arrow-right text-slate-500 group-hover:text-orange-400 transition transform group-hover:translate-x-1"></i>
            </div>
        </div>
    </a>

    {{-- CARD 3: TOP 10 --}}
    <a href="{{ route('admin.asset_health.top10') }}" class="bg-slate-900 border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-yellow-500/30 transition">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-yellow-500/10 rounded-full blur-xl group-hover:bg-yellow-500/20 transition"></div>
        
        <div class="relative">
            <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center text-yellow-400 mb-4">
                <i class="fa-solid fa-ranking-star text-lg"></i>
            </div>
            
            <div class="text-3xl font-bold text-white mb-1">Top 10</div>
            <div class="text-sm text-slate-400">Paling Sering Rusak</div>
            
            <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                <span class="text-xs text-slate-500">Lihat ranking</span>
                <i class="fa-solid fa-arrow-right text-slate-500 group-hover:text-yellow-400 transition transform group-hover:translate-x-1"></i>
            </div>
        </div>
    </a>

    {{-- CARD 4: HILANG --}}
    <a href="{{ route('admin.asset_health.hilang') }}" class="bg-slate-900 border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-red-500/30 transition">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-500/10 rounded-full blur-xl group-hover:bg-red-500/20 transition"></div>
        
        <div class="relative">
            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500 mb-4">
                <i class="fa-solid fa-person-circle-question text-lg"></i>
            </div>
            
            <div class="text-3xl font-bold text-white mb-1">{{ $hilangCount }}</div>
            <div class="text-sm text-slate-400">Asset Health - Hilang</div>
            
            <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between">
                <span class="text-xs text-slate-500">Lihat detail</span>
                <i class="fa-solid fa-arrow-right text-slate-500 group-hover:text-red-500 transition transform group-hover:translate-x-1"></i>
            </div>
        </div>
    </a>
</div>

{{-- RECENT ISSUES TABLE (Optional, but good to have) --}}
{{-- <div class="bg-slate-900 border border-white/10 rounded-2xl p-6">
    <h3 class="text-lg font-bold text-white mb-4">Masalah Aset Terbaru</h3>
   
</div> --}}
@endsection

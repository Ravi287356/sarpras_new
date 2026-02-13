@extends('layouts.app')

@section('content')
<div class="max-w-5xl">
    <div class="mb-5">
        <h1 class="text-2xl font-semibold tracking-tight">Pengaturan Checklist Inspeksi</h1>
        <p class="text-slate-400 text-sm mt-1">Pilih Sarpras untuk mengelola item checklist yang akan diperiksa.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($sarpras as $s)
            <a href="{{ route('admin.inspeksi.checklists.show', $s->id) }}" 
               class="group p-5 rounded-2xl bg-slate-900/40 ring-1 ring-white/10 hover:ring-emerald-500/30 hover:bg-slate-800/40 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $s->kategori->nama ?? 'Tanpa Kategori' }}</span>
                    <i class="bi bi-chevron-right text-slate-600 group-hover:text-emerald-400 transition"></i>
                </div>
                <h3 class="text-white font-medium group-hover:text-emerald-400 transition">{{ $s->nama }}</h3>
                <div class="mt-4 flex items-center gap-2 overflow-hidden">
                    @php 
                        $count = \App\Models\InspectionChecklist::where('sarpras_id', $s->id)->count(); 
                    @endphp
                    <span class="text-xs text-slate-500">
                        {{ $count }} item checklist
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection

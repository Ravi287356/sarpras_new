@extends('layouts.app')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.inspeksi.checklists.index') }}" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white transition">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Checklist: {{ $sarpras->nama }}</h1>
            <p class="text-slate-400 text-sm mt-1">Daftar hal yang harus diperiksa pada saat inspeksi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- List Checklist -->
        <div class="md:col-span-2 space-y-3">
            @forelse($checklists as $checklist)
                <div x-data="{ editing: false, tujuan: '{{ $checklist->tujuan_periksa }}' }" 
                     class="p-4 rounded-xl bg-slate-900/40 border border-white/5 flex items-center justify-between group">
                    
                    <div x-show="!editing" class="flex-1">
                        <span class="text-slate-200">{{ $checklist->tujuan_periksa }}</span>
                    </div>

                    <div x-show="editing" class="flex-1 mr-4">
                        <form action="{{ route('admin.inspeksi.checklists.update', $checklist->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="tujuan_periksa" x-model="tujuan" required
                                   class="flex-1 bg-slate-950 border border-slate-700 rounded-lg px-3 py-1 text-sm text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <div class="flex items-center gap-1">
                                <button type="submit" class="p-1 px-2 rounded bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold transition">
                                    Simpan
                                </button>
                                <button type="button" @click="editing = false; tujuan = '{{ $checklist->tujuan_periksa }}'" class="p-1 px-2 rounded bg-slate-800 hover:bg-slate-700 text-slate-400 text-[10px] font-bold transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>

                    <div x-show="!editing" class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click="editing = true" class="p-2 rounded-lg text-slate-500 hover:bg-blue-500/10 hover:text-blue-400 transition">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <form action="{{ route('admin.inspeksi.checklists.destroy', $checklist->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-lg text-slate-500 hover:bg-rose-500/10 hover:text-rose-400 transition">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center rounded-2xl bg-slate-900/40 border border-dashed border-slate-800">
                    <p class="text-slate-500 text-sm italic">Belum ada item checklist.</p>
                </div>
            @endforelse
        </div>

        <!-- Add Form -->
        <div class="md:col-span-1">
            <div class="p-6 rounded-2xl bg-emerald-500/5 ring-1 ring-emerald-500/20">
                <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-widest mb-4">Tambah Item</h3>
                <form action="{{ route('admin.inspeksi.checklists.store', $sarpras->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Tujuan Periksa</label>
                            <input type="text" name="tujuan_periksa" required placeholder="Contoh: Cek Layar"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 transition">
                        </div>
                        <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-xl shadow-lg shadow-emerald-600/20 transition">
                            Simpan Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

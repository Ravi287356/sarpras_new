@extends('layouts.app')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.sarpras.items', $item->sarpras_id) }}" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white transition">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Inspeksi Aset</h1>
            <p class="text-slate-400 text-sm mt-1">Item: <span class="text-emerald-400 font-mono">{{ $item->kode }}</span> ({{ $item->sarpras->nama }})</p>
        </div>
    </div>

    <form action="{{ route('admin.inspeksi.store', $item->id) }}" method="POST">
        @csrf
        <div class="space-y-6">
            <!-- Header Info -->
            <div class="p-6 rounded-2xl bg-slate-900/40 ring-1 ring-white/10 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Tanggal Inspeksi</label>
                    <input type="datetime-local" name="tanggal_inspeksi" required 
                           value="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 transition">
                </div>
                <div>
                    <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Inspektor</label>
                    <div class="px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-xl text-sm text-slate-300">
                        {{ auth()->user()->username }}
                    </div>
                </div>
            </div>

            <!-- Checklist Items -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest px-2">Daftar Pemeriksaan</h3>
                @foreach($checklists as $checklist)
                    <div class="p-5 rounded-2xl bg-slate-900/40 ring-1 ring-white/10 group hover:ring-white/20 transition">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <h4 class="text-slate-200 font-medium mb-1">{{ $checklist->tujuan_periksa }}</h4>
                                <p class="text-[10px] text-slate-500 italic">Pastikan kondisi sesuai standar operasional</p>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                @foreach(['Baik', 'Rusak', 'N/A'] as $status)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="results[{{ $checklist->id }}][status]" value="{{ $status }}" required class="peer sr-only">
                                        <div class="px-4 py-2 rounded-xl text-xs font-bold transition-all border
                                            @if($status == 'Baik') border-emerald-500/30 text-slate-500 peer-checked:bg-emerald-500/10 peer-checked:text-emerald-400 peer-checked:border-emerald-500/50 
                                            @elseif($status == 'Rusak') border-rose-500/30 text-slate-500 peer-checked:bg-rose-500/10 peer-checked:text-rose-400 peer-checked:border-rose-500/50
                                            @else border-slate-500/30 text-slate-500 peer-checked:bg-slate-500/10 peer-checked:text-slate-300 peer-checked:border-slate-500/50 @endif">
                                            {{ $status }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4">
                            <input type="text" name="results[{{ $checklist->id }}][catatan]" 
                                   placeholder="Tambahkan catatan khusus item ini (opsional)"
                                   class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition">
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- General Notes -->
            <div class="p-6 rounded-2xl bg-slate-900/40 ring-1 ring-white/10">
                <label class="block text-xs text-slate-500 uppercase font-bold mb-2">Catatan Umum / Rekomendasi</label>
                <textarea name="catatan_umum" rows="3" placeholder="Tulis catatan atau temuan penting lainnya di sini..."
                          class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 transition"></textarea>
            </div>

            <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-2xl shadow-xl shadow-emerald-500/20 transition-all flex items-center justify-center gap-2">
                <i class="bi bi-check2-circle"></i> Simpan Hasil Inspeksi
            </button>
        </div>
    </form>
</div>
@endsection

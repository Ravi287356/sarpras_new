@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-white">
                📑 Riwayat Pengaduan
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Daftar pengaduan yang pernah kamu ajukan
            </p>
        </div>

        <a href="{{ route('user.pengaduan.create') }}"
           class="px-4 py-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30
                  ring-1 ring-emerald-500/30 text-emerald-200 transition">
            + Ajukan Pengaduan
        </a>
    </div>

    <!-- Table -->
    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 overflow-hidden">
        <table class="w-full text-sm text-slate-200">
            <thead class="bg-slate-800/50 text-slate-300">
                <tr>
                    <th class="px-5 py-3 text-left">Judul</th>
                    <th class="px-5 py-3 text-center">Lokasi</th>
                    <th class="px-5 py-3 text-center">Status</th>
                    <th class="px-5 py-3 text-center">Tanggal</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800" x-data="{ expandedId: null }">
                @forelse ($pengaduan as $item)
                        <td class="px-5 py-3 text-white">
                            <div class="flex items-start gap-2">
                                <button @click="expandedId = (expandedId === {{ $item->id }} ? null : {{ $item->id }})" 
                                        class="mt-1 text-slate-400 hover:text-white transition-all transform"
                                        :class="expandedId === {{ $item->id }} ? 'rotate-90 text-emerald-400' : ''">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                                <div class="flex-1">
                                    <p class="text-white font-semibold leading-tight mb-1">{{ $item->judul }}</p>
                                    <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed italic">
                                        {{ $item->deskripsi }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-3 text-center text-slate-300">
                            {{ $item->lokasi?->nama ?? '-' }}
                        </td>


                        <td class="px-5 py-3 text-center">
                            @php
                                $statusMap = [
                                    'Belum Ditindaklanjuti' => 'Menunggu',
                                    'Sedang Diproses' => 'Diproses',
                                    'Selesai' => 'Selesai',
                                    'Ditutup' => 'Ditutup',
                                ];
                                $latestNote = $item->catatanPengaduan->last();
                            @endphp
                            <div class="flex flex-col items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs
                                             @if($item->status == 'Belum Ditindaklanjuti') bg-yellow-500/20 text-yellow-200 ring-1 ring-yellow-500/30
                                             @elseif($item->status == 'Sedang Diproses') bg-blue-500/20 text-blue-200 ring-1 ring-blue-500/30
                                             @elseif($item->status == 'Selesai') bg-emerald-500/20 text-emerald-200 ring-1 ring-emerald-500/30
                                             @else bg-gray-500/20 text-gray-200 ring-1 ring-gray-500/30 @endif">
                                    {{ $statusMap[$item->status] ?? $item->status }}
                                </span>
                                @if($latestNote)
                                    <p class="text-[10px] text-slate-400 italic max-w-[120px] line-clamp-2 leading-tight">
                                        "{{ $latestNote->catatan }}"
                                    </p>
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-3 text-center text-slate-400">
                            {{ $item->created_at->format('d-m-Y H:i') }}
                        </td>
                    </tr>

                    {{-- Detail Accordion --}}
                    <tr x-show="expandedId === {{ $item->id }}" x-cloak class="bg-slate-900/60 transition-all border-l-2 border-emerald-500/50">
                        <td colspan="4" class="px-12 py-6 border-t border-slate-800">
                            <div class="max-w-4xl">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-2">
                                        <p class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-bold">Detail & Riwayat Tanggapan</p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] text-slate-500 uppercase">Kategori:</span>
                                            <span class="text-xs text-slate-200">{{ $item->kategori->nama ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <div class="space-y-3 relative before:absolute before:left-0 before:top-2 before:bottom-2 before:w-px before:bg-slate-800 ml-1 pl-4">
                                        @forelse($item->catatanPengaduan as $cat)
                                            <div class="relative group">
                                                <div class="absolute -left-5 top-2 w-2 h-2 rounded-full bg-emerald-500/50 ring-4 ring-slate-900 shadow-lg shadow-emerald-500/20"></div>
                                                <div class="p-4 rounded-2xl bg-slate-800/40 border border-slate-800/50 group-hover:bg-slate-800/60 transition">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">{{ $cat->user->username ?? 'Petugas' }}</span>
                                                        <span class="text-[10px] text-slate-500 font-mono">{{ $cat->created_at->format('d M Y H:i') }}</span>
                                                    </div>
                                                    <p class="text-sm text-slate-300 leading-relaxed">{{ $cat->catatan }}</p>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-sm text-slate-500 italic py-2">Belum ada tanggapan atau catatan petugas.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-slate-500">
                            Belum ada pengaduan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $pengaduan->links() }}
        </div>
    </div>

    </div>

</div>

@endsection

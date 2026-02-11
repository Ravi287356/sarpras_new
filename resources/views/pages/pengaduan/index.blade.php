@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">🛠️ Data Pengaduan</h1>
            <p class="text-slate-400 text-sm mt-1">
                Kelola dan tindak lanjuti pengaduan pengguna
            </p>
        </div>

        <!-- Filter & Export -->
        <div class="flex items-center gap-3">
            <form method="GET" class="flex gap-2">
                <select name="lokasi_id" class="px-3 py-2 rounded-lg bg-slate-950 text-white ring-1 ring-slate-700">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasi as $lok)
                        <option value="{{ $lok->id }}" {{ request('lokasi_id') == $lok->id ? 'selected' : '' }}>
                            {{ $lok->nama }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="px-3 py-2 rounded-lg bg-slate-950 text-white ring-1 ring-slate-700">
                    <option value="">Semua Status</option>
                    @foreach(['Belum Ditindaklanjuti','Sedang Diproses','Selesai','Ditutup'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                            {{ $st }}
                        </option>
                    @endforeach
                </select>

                <button class="px-4 py-2 bg-emerald-600/20 ring-1 ring-emerald-500/30 rounded-lg">
                    Filter
                </button>
            </form>
            
            <a href="{{ route(auth()->user()->role->nama . '.pengaduan.export') }}" 
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-sm font-medium transition shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-file-csv mr-2"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-800/50 text-slate-300">
                <tr>
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Judul</th>
                    <th class="px-5 py-3">Lokasi</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800" x-data="{ expandedId: null }">
                @forelse($pengaduan as $item)
                <tr class="hover:bg-slate-800/40 transition">
                    <td class="px-5 py-3">{{ $item->user->username ?? '-' }}</td>
                    <td class="px-5 py-3">
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
                    <td class="px-5 py-3 text-slate-300">{{ $item->lokasi->nama ?? '-' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider
                            @if($item->status == 'Belum Ditindaklanjuti') bg-yellow-500/10 text-yellow-500 ring-1 ring-yellow-500/20
                            @elseif($item->status == 'Sedang Diproses') bg-blue-500/10 text-blue-500 ring-1 ring-blue-500/20
                            @elseif($item->status == 'Selesai') bg-emerald-500/10 text-emerald-500 ring-1 ring-emerald-500/20
                            @else bg-slate-500/10 text-slate-400 ring-1 ring-slate-500/20 @endif">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-slate-400">
                        {{ $item->created_at->format('d-m-Y H:i') }}
                    </td>
                    <td class="px-5 py-3 flex items-center gap-2">
                        @if(!in_array($item->status, ['Selesai', 'Ditutup']))
                        <a href="{{ auth()->user()->role->nama === 'admin'
                            ? route('admin.pengaduan.respond', $item->id)
                            : route('operator.pengaduan.respond', $item->id) }}"
                           class="px-3 py-2 rounded-lg bg-emerald-600/20 ring-1 ring-emerald-500/30 text-emerald-200 text-xs text-center flex-1">
                            Tanggapi
                        </a>
                        @endif
                    </td>
                </tr>
                
                {{-- Detail Accordion --}}
                <tr x-show="expandedId === {{ $item->id }}" x-cloak class="bg-slate-900/60 transition-all border-l-2 border-emerald-500/50">
                    <td colspan="6" class="px-12 py-6 border-t border-slate-800">
                        <div class="max-w-4xl">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-2">
                                    <p class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-bold">Detail & Riwayat Tanggapan</p>
                                    <div class="flex gap-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] text-slate-500 uppercase">Kategori:</span>
                                            <span class="text-xs text-slate-200">{{ $item->kategori->nama ?? '-' }}</span>
                                        </div>
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
                    <td colspan="6" class="text-center py-10 text-slate-500">
                        Data kosong
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $pengaduan->withQueryString()->links() }}
    </div>
</div>

@endsection

@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- WELCOME SECTION --}}
    <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 p-6">
        <div class="text-lg font-semibold mb-1">Selamat datang di sistem Sarpras!</div>
        <div class="text-slate-300 text-sm">Ringkasan data untuk admin.</div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5 hover:bg-slate-950/60 transition">
                <div class="text-slate-300 text-sm">Total User</div>
                <div class="text-2xl font-bold mt-2">{{ $totalUsers ?? 0 }}</div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5 hover:bg-slate-950/60 transition">
                <div class="text-slate-300 text-sm">Kategori Sarpras</div>
                <div class="text-2xl font-bold mt-2">{{ $totalKategori ?? 0 }}</div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5 hover:bg-slate-950/60 transition">
                <div class="text-slate-300 text-sm">Total Aset Rusak</div>
                <div class="text-2xl font-bold mt-2 text-rose-400">{{ $countRusak }}</div>
            </div>
        </div>
    </div>

    {{-- ASSET HEALTH REPORT SECTION --}}
    <div x-data="{ activeTab: 'overview' }" class="rounded-2xl border border-white/10 bg-slate-900 overflow-hidden">
        {{-- TABS NAVIGATION --}}
        <div class="flex border-b border-white/10 bg-slate-950/50">
            <button @click="activeTab = 'overview'"
                :class="activeTab === 'overview' ? 'border-blue-500 text-blue-400 bg-white/5' : 'border-transparent text-slate-400 hover:text-slate-200'"
                class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-all">
                <i class="fa-solid fa-chart-pie"></i> Overview
            </button>
            <button @click="activeTab = 'rusak'"
                :class="activeTab === 'rusak' ? 'border-rose-500 text-rose-400 bg-white/5' : 'border-transparent text-slate-400 hover:text-slate-200'"
                class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-all">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span> Alat Rusak ({{ $countRusak }})
            </button>
            <button @click="activeTab = 'top'"
                :class="activeTab === 'top' ? 'border-yellow-500 text-yellow-400 bg-white/5' : 'border-transparent text-slate-400 hover:text-slate-200'"
                class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-all">
                <i class="fa-solid fa-trophy text-yellow-500"></i> Top 10 Rusak
            </button>
            <button @click="activeTab = 'hilang'"
                :class="activeTab === 'hilang' ? 'border-red-500 text-red-400 bg-white/5' : 'border-transparent text-slate-400 hover:text-slate-200'"
                class="flex items-center gap-2 px-6 py-4 text-sm font-semibold border-b-2 transition-all">
                <i class="fa-solid fa-question text-red-500"></i> Alat Hilang ({{ $countHilang }})
            </button>
        </div>

        <div class="p-6">
            {{-- OVERVIEW TAB --}}
            <div x-show="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Condition Summary Card -->
                <div class="bg-slate-950/40 rounded-2xl border border-white/5 p-6 flex flex-col justify-center">
                    <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-blue-400"></i> Kondisi Aset
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-500/5 border border-emerald-500/10 hover:bg-emerald-500/10 transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-emerald-400 group-hover:scale-110 transition"><i class="fa-solid fa-check"></i></span>
                                <span class="text-emerald-500 font-semibold">Baik</span>
                            </div>
                            <span class="text-xl font-bold text-emerald-400">{{ $countBaik }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-rose-500/5 border border-rose-500/10 hover:bg-rose-500/10 transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-rose-400 group-hover:scale-110 transition"><i class="fa-solid fa-xmark"></i></span>
                                <span class="text-rose-500 font-semibold">Rusak</span>
                            </div>
                            <span class="text-xl font-bold text-rose-400">{{ $countRusak }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-xl bg-yellow-500/5 border border-yellow-500/10 hover:bg-yellow-500/10 transition group">
                            <div class="flex items-center gap-3">
                                <span class="text-yellow-400 group-hover:scale-110 transition"><i class="fa-solid fa-question-circle"></i></span>
                                <span class="text-yellow-600 font-semibold">Hilang</span>
                            </div>
                            <span class="text-xl font-bold text-yellow-500">{{ $countHilang }}</span>
                        </div>
                    </div>
                </div>

                <!-- Top Frequently Broken Card -->
                <div class="lg:col-span-2 bg-orange-500/5 rounded-2xl border border-orange-500/10 p-6">
                    <h3 class="text-white font-bold mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-fire text-orange-500"></i> Top 5 Alat Paling Sering Rusak
                    </h3>
                    <div class="space-y-3">
                        @forelse($topItems as $index => $item)
                        <div class="flex items-center gap-4 p-4 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 transition group">
                            <div class="flex-none w-8 text-center font-bold text-slate-500 group-hover:text-white transition">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="text-white font-semibold text-sm">{{ $item->nama }}</div>
                                <div class="text-xs text-slate-500">{{ $item->kategori->nama ?? '-' }}</div>
                            </div>
                            <div class="flex-none">
                                <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 font-bold text-xs">
                                    {{ $item->rusak_count }} item
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="py-12 text-center text-slate-500 italic text-sm">Belum ada data kerusakan tercatat</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- RUSAK TAB --}}
            <div x-show="activeTab === 'rusak'">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-400">
                        <i class="fa-solid fa-hammer text-sm"></i>
                    </span>
                    Asset Health - Rusak
                </h3>
                <div class="bg-slate-950/50 rounded-xl border border-white/5 overflow-hidden">
                    <table class="w-full text-sm text-slate-400">
                        <thead class="bg-slate-950 text-slate-200 text-xs uppercase border-b border-white/10">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode</th>
                                <th class="px-4 py-3 text-left">Nama Barang</th>
                                <th class="px-4 py-3 text-left">Kondisi</th>
                                <th class="px-4 py-3 text-left">Sejak Kapan</th>
                                <th class="px-4 py-3 text-left">Lokasi</th>
                                <th class="px-4 py-3 text-left">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($rusakItems as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3 font-mono text-xs text-slate-300">{{ $item->kode }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-white text-sm">{{ $item->sarpras->nama }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->sarpras->kategori->nama ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        {{ $item->kondisi->nama ?? 'Rusak' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">{{ $item->updated_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">{{ $item->lokasi->nama ?? '-' }}</td>
                                <td class="px-4 py-3 max-w-xs truncate text-xs" title="{{ $item->last_return_item->deskripsi_kerusakan ?? '-' }}">
                                    {{ $item->last_return_item->deskripsi_kerusakan ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500 italic">Tidak ada aset rusak</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TOP TAB --}}
            <div x-show="activeTab === 'top'">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center text-yellow-500">
                        <i class="fa-solid fa-ranking-star text-sm"></i>
                    </span>
                    Asset Health - Top 10 Sering Rusak
                </h3>
                <div class="bg-slate-950/50 rounded-xl border border-white/5 overflow-hidden">
                    <table class="w-full text-sm text-slate-400">
                        <thead class="bg-slate-950 text-slate-200 text-xs uppercase border-b border-white/10">
                            <tr>
                                <th class="px-4 py-3 text-left">Rank</th>
                                <th class="px-4 py-3 text-left">Nama Barang</th>
                                <th class="px-4 py-3 text-center">Jumlah Kerusakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($topItems as $index => $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3">#{{ $index + 1 }}</td>

                                <td class="px-4 py-3">
                                    <div class="font-medium text-white text-sm">{{ $item->nama }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->kategori->nama ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center text-rose-400 font-bold">{{ $item->rusak_count }} item</td>

                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500 italic">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- HILANG TAB --}}
            <div x-show="activeTab === 'hilang'">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500">
                        <i class="fa-solid fa-person-circle-question text-sm"></i>
                    </span>
                    Asset Health - Hilang
                </h3>
                <div class="bg-slate-950/50 rounded-xl border border-white/5 overflow-hidden">
                    <table class="w-full text-sm text-slate-400">
                        <thead class="bg-slate-950 text-slate-200 text-xs uppercase border-b border-white/10">
                            <tr>
                                <th class="px-4 py-3 text-left">Kode</th>
                                <th class="px-4 py-3 text-left">Nama Barang</th>
                                <th class="px-4 py-3 text-left">Peminjam Terakhir</th>
                                <th class="px-4 py-3 text-left">Tgl Update</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($hilangItems as $item)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3 font-mono text-xs text-slate-300">{{ $item->kode }}</td>
                                <td class="px-4 py-3 text-white font-medium">{{ $item->sarpras->nama }}</td>
                                <td class="px-4 py-3">{{ $item->last_peminjaman->user->username ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $item->updated_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500 italic">Tidak ada aset hilang</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

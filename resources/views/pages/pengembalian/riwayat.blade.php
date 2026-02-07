@extends('layouts.app')

@section('content')
<div class="max-w-7xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">{{ $title }}</h1>
        <p class="text-slate-300 text-sm mt-1">Daftar riwayat pengembalian sarpras.</p>
    </div>

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5">
                    <tr>
                        <th class="px-5 py-4 text-left">Kode Pinjam</th>
                        <th class="px-5 py-4 text-left">Peminjam</th>
                        <th class="px-5 py-4 text-left">Jumlah Barang</th>
                        <th class="px-5 py-4 text-left">Tgl Kembali</th>
                        <th class="px-5 py-4 text-left">Petugas</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/10">
                    @forelse($pengembalian as $row)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-5 py-4 font-mono text-blue-400">
                                {{ $row->peminjaman->kode_peminjaman ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-semibold text-white">{{ $row->peminjaman->user->username ?? '-' }}</div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="px-2 py-1 rounded bg-blue-500/10 text-blue-400 font-mono text-xs">
                                    {{ $row->items->count() }} Item
                                </span>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="text-white">{{ \Carbon\Carbon::parse($row->tanggal_pengembalian)->format('d-m-Y') }}</div>
                                <div class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($row->tanggal_pengembalian)->format('H:i') }}</div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="text-white font-medium">{{ $row->approvedBy->username ?? '-' }}</div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('pengembalian.detail', $row->id) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-lg transition shadow-sm">
                                    <i class="fa-solid fa-eye mr-1.5"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @if($row->catatan_petugas)
                            <tr class="bg-slate-900/20">
                                <td colspan="7" class="px-5 py-3 pt-0">
                                    <div class="flex gap-6 text-[11px]">
                                        <div>
                                            <span class="text-slate-500 font-bold uppercase mr-1">Catatan Petugas:</span>
                                            <span class="text-slate-300">{{ $row->catatan_petugas }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-slate-400">
                                Belum ada data pengembalian
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

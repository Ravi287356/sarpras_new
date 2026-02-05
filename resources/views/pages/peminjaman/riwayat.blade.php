@extends('layouts.app')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Riwayat Peminjaman</h1>
        <p class="text-slate-300 text-sm mt-1">Riwayat peminjaman milik akun kamu.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-500/10 text-emerald-200 ring-1 ring-emerald-500/30 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-slate-200">
                    <tr>
                        <th class="px-5 py-4 text-left">Kode Peminjaman</th>
                        <th class="px-5 py-4 text-left">Sarpras</th>
                        <th class="px-5 py-4 text-left">Jumlah</th>
                        <th class="px-5 py-4 text-left">Tgl Pinjam</th>
                        <th class="px-5 py-4 text-left">Est. Kembali</th>
                        <th class="px-5 py-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($logs as $row)
                        <tr class="text-slate-100">
                            <td class="px-5 py-4">
                                <span class="font-mono font-semibold text-blue-400">{{ $row->kode_peminjaman ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium">{{ $row->sarpras?->nama ?? '-' }}</div>
                                <div class="text-xs text-slate-400">
                                    {{ $row->sarpras?->kategori?->nama ?? '-' }} • {{ $row->sarpras?->lokasi?->nama ?? '-' }}
                                </div>
                            </td>
                            <td class="px-5 py-4">{{ (int)$row->jumlah }}</td>
                            <td class="px-5 py-4">
                                {{ \Carbon\Carbon::parse($row->tanggal_pinjam)->format('d-m-Y') }}
                            </td>
                            <td class="px-5 py-4">
                                {{ $row->tanggal_kembali_rencana ? \Carbon\Carbon::parse($row->tanggal_kembali_rencana)->format('d-m-Y') : '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-3 py-1 rounded-full text-xs ring-1
                                    {{ $row->status === 'dikembalikan' || $row->status === 'disetujui'
                                        ? 'bg-emerald-500/10 text-emerald-200 ring-emerald-500/30'
                                        : 'bg-red-800 text-red-200 ring-red-500' }}">
                                    {{ strtoupper($row->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                Belum ada riwayat peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection

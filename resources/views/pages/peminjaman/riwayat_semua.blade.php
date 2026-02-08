@extends('layouts.app')

@section('content')
<div class="max-w-7xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Riwayat Peminjaman (Semua User)</h1>
        <p class="text-slate-300 text-sm mt-1">Riwayat seluruh peminjaman sarpras.</p>
    </div>

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5">
                    <tr>
                        <th class="px-5 py-4">Kode</th>
                        <th class="px-5 py-4">User</th>
                        <th class="px-5 py-4">Sarpras</th>
                        <th class="px-5 py-4 text-center">Jumlah</th>
                        <th class="px-5 py-4">Tujuan</th>
                        <th class="px-5 py-4">Tgl Pinjam</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Disetujui Oleh</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/10">
                    @forelse($logs as $row)
                        <tr>
                            <td class="px-5 py-4 font-mono text-blue-400">
                                {{ $row->kode_peminjaman ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-semibold">{{ $row->user?->username }}</div>
                                <div class="text-xs text-slate-400">{{ $row->user?->role?->nama }}</div>
                            </td>

                            <td class="px-5 py-4">
                                {{ $row->sarpras?->nama }}
                            </td>

                            <td class="px-5 py-4 text-center">{{ $row->jumlah }}</td>

                            <td class="px-5 py-4">
                                {{ $row->tujuan ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                {{ \Carbon\Carbon::parse($row->tanggal_pinjam)->format('d-m-Y') }}
                            </td>

                            <td class="px-5 py-4">
                                @php
                                    $statusColor = match($row->status) {
                                        'dipinjam' => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
                                        'disetujui' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                                        'dikembalikan' => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
                                        'ditolak' => 'text-rose-400 bg-rose-400/10 border-rose-400/20',
                                        'menunggu' => 'text-slate-400 bg-slate-400/10 border-slate-400/20',
                                        default => 'text-slate-400 bg-slate-400/10 border-slate-400/20',
                                    };
                                @endphp
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center w-fit px-2 py-0.5 rounded text-xs font-medium border {{ $statusColor }}">
                                        {{ strtoupper($row->status) }}
                                    </span>
                                    @if($row->status === 'ditolak' && $row->alasan_penolakan)
                                        <div class="text-[10px] text-rose-300/70 italic leading-tight max-w-[150px]">
                                            Ket: {{ $row->alasan_penolakan }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="text-xs">
                                    <div class="font-medium text-slate-200">{{ $row->approver?->username ?? '-' }}</div>
                                    <div class="text-slate-500">{{ $row->status === 'ditolak' ? 'Penolak' : 'Approver' }}</div>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                @if(in_array($row->status, ['disetujui','dikembalikan']))
                                    <a href="{{ route((auth()->user()->role->nama === 'admin' ? 'admin' : 'operator').'.peminjaman.struk', $row->id) }}">
                                        📄 Struk
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-6 text-slate-400">
                                Belum ada data peminjaman
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

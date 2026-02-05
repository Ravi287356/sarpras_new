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
                                {{ strtoupper($row->status) }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $row->approver?->username ?? '-' }}
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

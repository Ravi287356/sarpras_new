@extends('layouts.app')

@section('content')
<div class="max-w-6xl">
    <h1 class="text-2xl font-semibold mb-4">Peminjaman Aktif</h1>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-400/30 bg-emerald-500/10 p-3 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-400/30 bg-red-500/10 p-3 text-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-white/5">
                <tr>
                    <th class="px-4 py-3 text-left">Disetujui</th>
                    <th class="px-4 py-3 text-left">User</th>
                    <th class="px-4 py-3 text-left">Sarpras</th>
                    <th class="px-4 py-3 text-center">Jumlah</th>
                    <th class="px-4 py-3 text-left">Tgl Pinjam</th>
                    <th class="px-4 py-3 text-left">Est. Kembali</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-white/10">
                @forelse ($items as $row)
                    <tr>
                        <td class="px-4 py-3">
                            {{ $row->approved_at ? \Carbon\Carbon::parse($row->approved_at)->format('d-m-Y H:i') : '-' }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $row->user?->username ?? '-' }}</div>
                            <div class="text-xs text-slate-300">{{ $row->user?->role?->nama ?? '-' }}</div>
                        </td>

                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $row->sarpras?->nama ?? '-' }}</div>
                            <div class="text-xs text-slate-300">
                                {{ $row->sarpras?->kategori?->nama ?? '-' }} • {{ $row->sarpras?->lokasi?->nama ?? '-' }}
                            </div>
                        </td>

                        <td class="px-4 py-3 text-center">{{ $row->jumlah }}</td>

                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($row->tanggal_pinjam)->format('d-m-Y') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $row->tanggal_kembali_rencana ? \Carbon\Carbon::parse($row->tanggal_kembali_rencana)->format('d-m-Y') : '-' }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-col gap-2">
                                <a href="{{ route((auth()->user()->role->nama === 'admin' ? 'admin' : 'operator').'.peminjaman.struk', $row->id) }}"
                                   class="px-3 py-2 rounded-xl border border-blue-400/50 bg-blue-500/10 hover:bg-blue-500/20 transition text-blue-300 text-xs">
                                    📄 Struk
                                </a>
                                <form method="POST"
                                    action="{{ route((auth()->user()->role->nama === 'admin' ? 'admin' : 'operator').'.peminjaman.kembalikan', $row->id) }}"
                                    onsubmit="return confirm('Tandai sebagai dikembalikan?')"
                                    class="m-0">
                                    @csrf
                                    @method('PUT')
                                    <button class="w-full px-3 py-2 rounded-xl border border-white/10 hover:bg-white/5 transition text-sm">
                                        ✓ Kembalikan
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-300">
                            Belum ada peminjaman aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

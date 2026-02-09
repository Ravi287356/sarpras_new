@extends('layouts.app')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Permintaan Peminjaman</h1>
        <p class="text-slate-300 text-sm mt-1">Daftar peminjaman dengan status <b>MENUNGGU</b>.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-500/10 text-emerald-200 ring-1 ring-emerald-500/30 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl bg-rose-500/10 text-rose-200 ring-1 ring-rose-500/30 px-4 py-3">
            <b>Gagal:</b>
            <ul class="list-disc list-inside mt-2 space-y-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-slate-200">
                    <tr>
                        <th class="px-5 py-4 text-left w-14">No</th>
                        <th class="px-5 py-4 text-left">Pemohon</th>
                        <th class="px-5 py-4 text-left">Sarpras</th>
                        <th class="px-5 py-4 text-left">Jumlah</th>
                        <th class="px-5 py-4 text-left">Tgl Pinjam</th>
                        <th class="px-5 py-4 text-left">Est. Kembali</th>
                        <th class="px-5 py-4 text-left">Tujuan</th>
                        <th class="px-5 py-4 text-left w-64">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/10">
                    @forelse($items as $i => $row)
                        <tr class="text-slate-100 align-top">
                            <td class="px-5 py-4">{{ $items->firstItem() + $i }}</td>

                            <td class="px-5 py-4">
                                <div class="font-medium">{{ $row->user?->username ?? '-' }}</div>
                                <div class="text-xs text-slate-400">{{ $row->user?->role?->nama ?? '-' }}</div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-medium">{{ $row->sarpras?->nama ?? '-' }}</div>
                                <div class="text-xs text-slate-400">
                                    {{ $row->sarpras?->kategori?->nama ?? '-' }}
                                    •
                                    {{ $row->sarpras?->items->first()?->lokasi?->nama ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-400 mt-1">
                                    Kode: {{ $row->sarpras?->items->first()?->kode ?? '-' }}
                                </div>
                            </td>

                            <td class="px-5 py-4">{{ (int) $row->jumlah }}</td>

                            <td class="px-5 py-4">
                                {{ \Carbon\Carbon::parse($row->tanggal_pinjam)->format('d-m-Y') }}
                            </td>

                            <td class="px-5 py-4">
                                {{ \Carbon\Carbon::parse($row->tanggal_kembali_rencana)->format('d-m-Y') }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $row->tujuan ?: '-' }}
                            </td>

                            <td class="px-5 py-4 space-y-2">
                                {{-- ✅ SETUJUI --}}
                                <form action="{{ route(auth()->user()?->role?->nama . '.peminjaman.setujui', $row->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PUT')

                                    <input type="text" name="alasan_persetujuan" placeholder="Alasan persetujuan (opsional)"
                                        class="w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-2
                                               focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition">

                                    <button type="submit"
                                        class="w-full px-4 py-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30
                                               text-emerald-200 ring-1 ring-emerald-500/30 transition">
                                        Setujui
                                    </button>
                                </form>

                                {{-- ❌ TOLAK + ALASAN --}}
                                <form action="{{ route(auth()->user()?->role?->nama . '.peminjaman.tolak', $row->id) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PUT')

                                    <input type="text" name="alasan_penolakan" placeholder="Alasan penolakan (Wajib)" required
                                        class="w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-2
                                               focus:outline-none focus:ring-2 focus:ring-rose-500/50 transition">

                                    <button type="submit"
                                        class="w-full px-4 py-2 rounded-xl bg-rose-600/15 hover:bg-rose-600/25
                                               text-rose-200 ring-1 ring-rose-500/30 transition">
                                        Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400">
                                Tidak ada permintaan peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection

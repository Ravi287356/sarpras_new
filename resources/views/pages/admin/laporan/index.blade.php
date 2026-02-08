@extends('layouts.app')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Laporan Administrasi</h1>
        <p class="text-slate-400 text-sm mt-1">Generate dan export laporan berdasarkan periode.</p>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded-xl bg-rose-500/10 text-rose-200 ring-1 ring-rose-500/30 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- FILTER FORM --}}
    <div class="bg-slate-900/40 ring-1 ring-white/10 rounded-2xl p-6 mb-8">
        <form action="{{ route('admin.laporan.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Jenis Laporan</label>
                <select name="type" class="w-full bg-slate-800 border-white/10 rounded-xl text-sm text-slate-100 focus:ring-emerald-500/50">
                    <option value="peminjaman" {{ $type === 'peminjaman' ? 'selected' : '' }}>Peminjaman</option>
                    <option value="pengaduan" {{ $type === 'pengaduan' ? 'selected' : '' }}>Pengaduan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" required
                    class="w-full bg-slate-800 border-white/10 rounded-xl text-sm text-slate-100 focus:ring-emerald-500/50">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" required
                    class="w-full bg-slate-800 border-white/10 rounded-xl text-sm text-slate-100 focus:ring-emerald-500/50">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold py-2.5 rounded-xl transition shadow-lg shadow-emerald-500/20">
                    Preview
                </button>
                @if($startDate && $endDate)
                    <a href="{{ route('admin.laporan.export', request()->all()) }}" 
                       class="flex-1 bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold py-2.5 rounded-xl transition text-center border border-white/10">
                        Export Excel
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- PREVIEW TABLE --}}
    @if($startDate && $endDate)
    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden shadow-xl">
        <div class="px-5 py-4 border-b border-white/10 bg-white/5 flex justify-between items-center">
            <h3 class="font-medium text-slate-200">Preview Data: {{ ucfirst($type) }}</h3>
            <span class="text-xs text-slate-400">{{ $data->count() }} records found</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                @if($type === 'peminjaman')
                    <thead class="bg-white/5 text-slate-200 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-5 py-3 text-left">No</th>
                            <th class="px-5 py-3 text-left">Kode</th>
                            <th class="px-5 py-3 text-left">Peminjam</th>
                            <th class="px-5 py-3 text-left">Tanggal Pinjam</th>
                            <th class="px-5 py-3 text-left">Barang (Jumlah)</th>
                            <th class="px-5 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse($data as $i => $row)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-5 py-4">{{ $i + 1 }}</td>
                                <td class="px-5 py-4 font-mono text-xs text-emerald-400">{{ $row->kode_peminjaman }}</td>
                                <td class="px-5 py-4">{{ $row->user?->name }}</td>
                                <td class="px-5 py-4">{{ \Carbon\Carbon::parse($row->tanggal_pinjam)->format('d M Y') }}</td>
                                <td class="px-5 py-4">
                                    <div class="text-xs">
                                        {{ $row->items->first()?->sarprasItem?->sarpras?->nama ?? '-' }} 
                                        @if($row->items->count() > 1)
                                            <span class="text-slate-500">(+{{ $row->items->count() - 1 }} lainnya)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-[11px]">
                                    <span class="px-2 py-0.5 rounded-full border border-white/10 {{ $row->status === 'disetujui' || $row->status === 'selesai' ? 'text-emerald-400 bg-emerald-400/5' : 'text-slate-400' }}">
                                        {{ strtoupper($row->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500 italic">Tidak ada data untuk periode ini.</td></tr>
                        @endforelse
                    </tbody>
                @else
                    {{-- PENGADUAN --}}
                    <thead class="bg-white/5 text-slate-200 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-5 py-3 text-left">No</th>
                            <th class="px-5 py-3 text-left">Pelapor</th>
                            <th class="px-5 py-3 text-left">Judul</th>
                            <th class="px-5 py-3 text-left">Lokasi</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-left">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-slate-300">
                        @forelse($data as $i => $row)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-5 py-4">{{ $i + 1 }}</td>
                                <td class="px-5 py-4">{{ $row->user?->name }}</td>
                                <td class="px-5 py-4 font-medium">{{ $row->judul }}</td>
                                <td class="px-5 py-4 text-xs text-slate-400">{{ $row->lokasi?->nama }}</td>
                                <td class="px-5 py-4 text-[11px]">
                                    <span class="px-2 py-0.5 rounded-full border border-white/10 {{ $row->status === 'selesai' ? 'text-emerald-400 bg-emerald-400/5' : 'text-slate-400' }}">
                                        {{ strtoupper($row->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs">{{ $row->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500 italic">Tidak ada data untuk periode ini.</td></tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

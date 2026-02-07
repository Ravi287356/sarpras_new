@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.asset_health.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-white/10 hover:bg-white/5 transition">
        <i class="fa-solid fa-arrow-left text-slate-400"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-white mb-1">{{ $title }}</h1>
        <p class="text-slate-400 text-sm">Daftar aset yang saat ini dalam kondisi rusak</p>
    </div>
</div>

<div class="bg-slate-900 border border-white/10 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-950 text-slate-200 uppercase font-medium border-b border-white/10">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Nama Barang</th>
                    <th class="px-6 py-4">Kondisi</th>
                    <th class="px-6 py-4">Sejak Kapan</th>
                    <th class="px-6 py-4">Lokasi</th>
                    <th class="px-6 py-4">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($items as $index => $item)
                <tr class="hover:bg-white/5 transition">
                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-mono text-slate-300">{{ $item->kode }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-white">{{ $item->sarpras->nama }}</div>
                        <div class="text-xs">{{ $item->sarpras->kategori->nama ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">
                            {{ $item->kondisi->nama ?? 'Rusak' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        {{ $item->updated_at->format('d M Y') }}
                        <div class="text-xs text-slate-500">{{ $item->updated_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4">{{ $item->lokasi->nama ?? '-' }}</td>
                    <td class="px-6 py-4 max-w-xs truncate" title="{{ $item->last_return->deskripsi_kerusakan ?? '-' }}">
                        {{ $item->last_return->deskripsi_kerusakan ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                        <i class="fa-solid fa-check-circle text-4xl mb-4 text-emerald-500/20"></i>
                        <p>Tidak ada aset yang rusak saat ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

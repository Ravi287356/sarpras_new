@extends('layouts.app')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Sarpras Tersedia</h1>
    </div>

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-slate-200">
                    <tr>
                        <th class="px-5 py-4 text-left w-14">No</th>
                        <th class="px-5 py-4 text-left">Kode</th>
                        <th class="px-5 py-4 text-left">Nama</th>
                        <th class="px-5 py-4 text-left">Kategori</th>
                        <th class="px-5 py-4 text-left">Lokasi</th>
                        <th class="px-5 py-4 text-left w-24">Stok</th>
                        <th class="px-5 py-4 text-left">Kondisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($items as $i => $row)
                        <tr class="text-slate-100">
                            <td class="px-5 py-4">{{ $i + 1 }}</td>
                            <td class="px-5 py-4">{{ $row->kode }}</td>
                            <td class="px-5 py-4 font-medium">{{ $row->nama }}</td>
                            <td class="px-5 py-4">{{ $row->kategori?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $row->lokasi?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $row->jumlah_stok }}</td>
                            <td class="px-5 py-4">{{ $row->kondisi_saat_ini ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                                Data sarpras belum ada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-white">
                📑 Riwayat Pengaduan
            </h1>
            <p class="text-slate-400 text-sm mt-1">
                Daftar pengaduan yang pernah kamu ajukan
            </p>
        </div>

        <a href="{{ route('user.pengaduan.create') }}"
           class="px-4 py-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30
                  ring-1 ring-emerald-500/30 text-emerald-200 transition">
            + Ajukan Pengaduan
        </a>
    </div>

    <!-- Table -->
    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 overflow-hidden">
        <table class="w-full text-sm text-slate-200">
            <thead class="bg-slate-800/50 text-slate-300">
                <tr>
                    <th class="px-5 py-3 text-left">Judul</th>
                    <th class="px-5 py-3">Lokasi</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Tanggal</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800">
                @forelse ($pengaduan as $item)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-5 py-3 font-medium text-white">
                            {{ $item->judul }}
                        </td>

                        <td class="px-5 py-3 text-center text-slate-300">
                            {{ $item->lokasi?->nama ?? '-' }}
                        </td>

                        <td class="px-5 py-3 text-center">
                            @php
                                $statusMap = [
                                    'Belum Ditindaklanjuti' => 'Menunggu',
                                    'Sedang Diproses' => 'Diproses',
                                    'Selesai' => 'Selesai',
                                    'Ditutup' => 'Ditutup',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs
                                         @if($item->status == 'Belum Ditindaklanjuti') bg-yellow-500/20 text-yellow-200 ring-1 ring-yellow-500/30
                                         @elseif($item->status == 'Sedang Diproses') bg-blue-500/20 text-blue-200 ring-1 ring-blue-500/30
                                         @elseif($item->status == 'Selesai') bg-emerald-500/20 text-emerald-200 ring-1 ring-emerald-500/30
                                         @else bg-gray-500/20 text-gray-200 ring-1 ring-gray-500/30 @endif">
                                {{ $statusMap[$item->status] ?? $item->status }}
                            </span>
                        </td>

                        <td class="px-5 py-3 text-center text-slate-400">
                            {{ $item->created_at->format('d-m-Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-slate-500">
                            Belum ada pengaduan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $pengaduan->links() }}
        </div>
    </div>

</div>
@endsection

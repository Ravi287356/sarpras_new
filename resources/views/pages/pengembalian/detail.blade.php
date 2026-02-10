@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $title }}
            </h1>
            <p class="text-sm text-gray-500">
                Detail pengembalian sarana dan prasarana
            </p>
        </div>
        <a href="{{ route('pengembalian.riwayat') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            <i class="fa-solid fa-arrow-left mr-2"></i>Kembali ke Riwayat
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="mb-8 pb-6 border-b last:border-b-0">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-semibold text-lg text-gray-800">
                        Informasi Pengembalian
                    </h3>
                    <p class="text-sm text-gray-600">
                        <i class="fa-solid fa-calendar mr-1"></i>
                        {{ \Carbon\Carbon::parse($pengembalian->created_at)->format('d-m-Y H:i') }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <i class="fa-solid fa-user mr-1"></i>
                        Petugas: {{ $pengembalian->approvedBy->username ?? '-' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <i class="fa-solid fa-user-tag mr-1"></i>
                        Peminjam: {{ $pengembalian->peminjaman->user->username ?? '-' }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Kode Peminjaman</p>
                    <p class="font-semibold text-gray-800">{{ $pengembalian->peminjaman->kode_peminjaman ?? '-' }}</p>
                </div>
            </div>

            @if ($pengembalian->catatan_petugas)
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <p class="text-sm text-gray-700">
                        <strong>Catatan Petugas:</strong> {{ $pengembalian->catatan_petugas }}
                    </p>
                </div>
            @endif

            <!-- Items Details -->
            <div class="mt-4">
                <h4 class="font-medium text-gray-700 mb-3">
                    <i class="fa-solid fa-box mr-1"></i> Barang yang Dikembalikan:
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($pengembalian->items as $pengembalianItem)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="mb-2">
                                <p class="font-semibold text-gray-800">
                                    {{ optional($pengembalianItem->sarprasItem->sarpras)->nama ?? '-' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Kode: {{ $pengembalianItem->sarprasItem->kode ?? '-' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                   Lokasi: {{ optional($pengembalianItem->sarprasItem->lokasi)->nama ?? '-' }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between mt-3">
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Kondisi:</p>
                                    @php
                                        $kondisi = $pengembalianItem->kondisiAlat->nama ?? '-';
                                    @endphp

                                    @if ($kondisi === 'Baik')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold">
                                            {{ $kondisi }}
                                        </span>
                                    @elseif (in_array($kondisi, ['Rusak Ringan', 'Rusak Berat']))
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">
                                            {{ $kondisi }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700 font-semibold">
                                            {{ $kondisi }}
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    @if ($pengembalianItem->foto_url)
                                        <a href="{{ asset('storage/' . $pengembalianItem->foto_url) }}"
                                           target="_blank"
                                           class="text-blue-600 hover:underline text-sm">
                                            <i class="fa-solid fa-image mr-1"></i> Lihat Foto
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic text-xs">
                                            Tanpa foto
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if ($pengembalianItem->deskripsi_kerusakan)
                                <div class="mt-3 pt-3 border-t border-blue-100">
                                    <p class="text-xs text-gray-500 font-semibold mb-1">Keterangan Kerusakan:</p>
                                    <p class="text-sm text-gray-700 italic">
                                        "{{ $pengembalianItem->deskripsi_kerusakan }}"
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

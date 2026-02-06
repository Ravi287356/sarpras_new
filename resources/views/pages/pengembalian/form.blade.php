@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Konfirmasi Pengembalian Barang</h1>
                <p class="text-sm text-gray-500">Kode Peminjaman: <span
                        class="font-semibold text-gray-700">{{ $peminjaman->kode_peminjaman }}</span></p>
            </div>
            <a href="{{ route('pengembalian.index') }}"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Peminjam</p>
                    <p class="font-semibold text-gray-800">{{ $peminjaman->user->username }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tanggal Pinjam</p>
                    <p class="font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tanggal Rencana Kembali</p>
                    <p class="font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <span class="inline-block px-3 py-1 bg-blue-200 text-blue-800 rounded-full text-xs font-semibold">
                        {{ $peminjaman->statusPinjam->nama }}
                    </span>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-blue-200">
                <p class="text-sm font-semibold text-gray-700 mb-3">Barang yang Dipinjam:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($peminjaman->peminjamanItem as $item)
                        <div class="bg-white p-4 rounded-lg border border-blue-100">
                            <p class="font-semibold text-gray-800">{{ optional($item->sarprasItem->sarpras)->nama ?? '-' }}
                            </p>
                            <p class="text-sm text-gray-600">Kode: {{ $item->sarprasItem->kode }}</p>
                            <p class="text-sm text-gray-600">Lokasi: {{ optional($item->sarprasItem->lokasi)->nama ?? '-' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-8">
            <h2 class="text-lg font-bold text-gray-800 mb-6">Data Pengembalian</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded-lg mb-6">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pengembalian.store') }}" enctype="multipart/form-data"
                class="space-y-6">
                @csrf
                <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kondisi Barang Saat Kembali <span
                            class="text-red-500">*</span></label>
                    <select name="kondisi_alat_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                               @error('kondisi_alat_id') @enderror">
                        <option value="">-- Pilih Kondisi --</option>
                        @foreach ($kondisiAlat as $kondisi)
                            <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                        @endforeach
                    </select>
                    @error('kondisi_alat_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Kerusakan / Catatan
                        (Opsional)</label>
                    <textarea name="deskripsi_kerusakan" placeholder="Contoh: Lensa sedikit berdebu, lampu mulai berkedip, dll"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                               @error('deskripsi_kerusakan') @enderror"></textarea>
                    @error('deskripsi_kerusakan')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Petugas<span
                            class="text-red-500">*</span></label>
                    <textarea name="catatan_petugas" placeholder="Contoh: Perlu pembersihan dan penggantian lampu" rows="3" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    @error('catatan_petugas')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Foto Kondisi Barang (Opsional)
                    </label>

                    <input type="file" name="foto" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg
               focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <p class="text-xs text-gray-500 mt-1">
                        Format: JPG, PNG, maksimal 2MB
                    </p>

                    @error('foto')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>


                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <p class="text-sm text-yellow-800">
                        <i class="fa-solid fa-lightbulb mr-2"></i>
                        <strong>Info:</strong> Jika kondisi adalah "Rusak Ringan" atau "Rusak Berat", status barang akan
                        otomatis berubah menjadi "Butuh Maintenance".
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t">
                    <a href="{{ route('pengembalian.index') }}"
                        class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fa-solid fa-check mr-2"></i>Konfirmasi Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

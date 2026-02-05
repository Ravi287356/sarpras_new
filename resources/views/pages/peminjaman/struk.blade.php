@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Tombol Print & Back -->
    <div class="flex gap-2 mb-4">
        <button onclick="window.print()" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 transition">
            🖨️ Cetak
        </button>
        <a href="javascript:history.back()" class="px-4 py-2 rounded-lg bg-slate-600 hover:bg-slate-700 transition">
            ← Kembali
        </a>
    </div>

    <!-- Struk Container -->
    <div class="bg-white text-black p-8 rounded-lg shadow-lg print:shadow-none" id="struk">
        <!-- Header -->
        <div class="text-center mb-6 border-b-2 pb-4">
            <h1 class="text-2xl font-bold">STRUK PEMINJAMAN</h1>
            <p class="text-sm text-gray-600 mt-1">Sistem Manajemen Sarpras</p>
        </div>

        <!-- Kode Peminjaman dengan QR Code -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="border-l-4 border-blue-600 pl-4">
                <p class="text-xs text-gray-600 font-semibold">KODE PEMINJAMAN</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $peminjaman->kode_peminjaman }}</p>
                <p class="text-xs text-gray-500 mt-2">Tgl: {{ now()->format('d-m-Y H:i') }}</p>
            </div>
            
            <div class="flex justify-center">
                <div class="text-center">
                    {!! $qrCode !!}
                    <p class="text-xs text-gray-600 mt-2">QR Code Peminjaman</p>
                </div>
            </div>
        </div>

        <hr class="my-6">

        <!-- Informasi Peminjam -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-xs font-semibold text-gray-600 uppercase">Peminjam</p>
                <p class="text-lg font-bold mt-2">{{ $peminjaman->user->username }}</p>
                <p class="text-sm text-gray-600">{{ $peminjaman->user->name ?? '-' }}</p>
                <p class="text-sm text-gray-600">📧 {{ $peminjaman->user->email ?? '-' }}</p>
                <p class="text-sm text-gray-600 mt-2"><span class="badge badge-primary">{{ $peminjaman->user->role->nama ?? '-' }}</span></p>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-600 uppercase">Disetujui Oleh</p>
                <p class="text-lg font-bold mt-2">{{ $peminjaman->approver?->username ?? '-' }}</p>
                <p class="text-sm text-gray-600">{{ $peminjaman->approver?->name ?? '-' }}</p>
                <p class="text-sm text-gray-600 mt-3">
                    <strong>Tgl Disetujui:</strong><br>
                    {{ $peminjaman->approved_at ? \Carbon\Carbon::parse($peminjaman->approved_at)->format('d-m-Y H:i') : '-' }}
                </p>
            </div>
        </div>

        <hr class="my-6">

        <!-- Informasi Sarpras -->
        <div class="mb-6">
            <p class="text-xs font-semibold text-gray-600 uppercase mb-3">Detail Sarpras</p>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-600">Nama Sarpras</p>
                        <p class="font-bold text-lg">{{ $peminjaman->sarpras->nama }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Kode Sarpras</p>
                        <p class="font-bold">{{ $peminjaman->sarpras->kode_sarpras ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Kategori</p>
                        <p class="font-semibold">{{ $peminjaman->sarpras->kategori->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Lokasi</p>
                        <p class="font-semibold">{{ $peminjaman->sarpras->lokasi->nama ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Peminjaman -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <p class="text-xs text-gray-600 font-semibold">JUMLAH</p>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $peminjaman->jumlah }}</p>
            </div>

            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <p class="text-xs text-gray-600 font-semibold">TGL PINJAM</p>
                <p class="text-lg font-bold text-green-600 mt-2">
                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d-m-Y') }}
                </p>
            </div>

            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                <p class="text-xs text-gray-600 font-semibold">EST. KEMBALI</p>
                <p class="text-lg font-bold text-orange-600 mt-2">
                    {{ $peminjaman->tanggal_kembali_rencana ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d-m-Y') : '-' }}
                </p>
            </div>
        </div>

        @if($peminjaman->tujuan)
            <div class="mb-6">
                <p class="text-xs font-semibold text-gray-600 uppercase mb-2">Tujuan Peminjaman</p>
                <p class="text-sm p-3 bg-gray-50 rounded border border-gray-200">{{ $peminjaman->tujuan }}</p>
            </div>
        @endif

        <!-- Status -->
        <div class="mb-6 p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 uppercase">Status</p>
            <p class="text-lg font-bold text-emerald-600 mt-1">✅ {{ strtoupper($peminjaman->status) }}</p>
        </div>

        <!-- Footer -->
        <hr class="my-6">
        <div class="text-center text-xs text-gray-600">
            <p>Struk ini adalah bukti sah peminjaman sarpras</p>
            <p class="mt-1">Harap disimpan dengan baik</p>
            <p class="mt-3">{{ config('app.name') }} | Generated: {{ now()->format('d-m-Y H:i:s') }}</p>
        </div>
    </div>
</div>

<style media="print">
    body {
        margin: 0;
        padding: 0;
    }
    
    .max-w-2xl {
        max-width: 100%;
    }
    
    #struk {
        box-shadow: none;
        page-break-after: avoid;
    }
    
    button, a {
        display: none;
    }
</style>
@endsection

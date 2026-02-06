@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto">

    <!-- ================= TOMBOL ================= -->
    <div class="mb-4 flex gap-2 no-print">
        <button onclick="window.print()" class="px-4 py-2 border border-black">
            Cetak
        </button>

        <button onclick="history.back()" class="px-4 py-2 border border-black">
            Kembali
        </button>
    </div>

    <!-- ================= STRUK ================= -->
    <div id="struk" class="bg-white text-black p-6 border border-black">

        <div class="text-center mb-4">
            <h1 class="text-lg font-bold uppercase">
                Struk Peminjaman Sarpras
            </h1>
            <p class="text-sm">{{ config('app.name') }}</p>
        </div>

        <hr class="border-black mb-4">

        <table class="w-full text-sm mb-4">
            <tr>
                <td class="w-40 font-semibold">Kode Peminjaman</td>
                <td>: {{ $peminjaman->kode_peminjaman }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Tanggal Cetak</td>
                <td>: {{ now()->format('d-m-Y H:i') }}</td>
            </tr>
        </table>

        <hr class="border-black mb-4">

        <p class="font-semibold mb-2">Data Peminjam</p>
        <table class="w-full text-sm mb-4">
            <tr>
                <td class="w-40">Username</td>
                <td>: {{ $peminjaman->user->username }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>: {{ $peminjaman->user->email ?? '-' }}</td>
            </tr>
            <tr>
                <td>Role</td>
                <td>: {{ $peminjaman->user->role->nama ?? '-' }}</td>
            </tr>
        </table>

        <hr class="border-black mb-4">

        <p class="font-semibold mb-2">Data Sarpras</p>
        <table class="w-full text-sm mb-4">
            <tr>
                <td class="w-40">Nama Sarpras</td>
                <td>: {{ $peminjaman->sarpras->nama }}</td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>: {{ $peminjaman->sarpras->kategori->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td>Lokasi</td>
                <td>: {{ $peminjaman->sarpras->lokasi->nama ?? '-' }}</td>
            </tr>
        </table>

        <hr class="border-black mb-4">

        <p class="font-semibold mb-2">Detail Peminjaman</p>
        <table class="w-full text-sm mb-4">
            <tr>
                <td class="w-40">Jumlah</td>
                <td>: {{ $peminjaman->jumlah }}</td>
            </tr>
            <tr>
                <td>Tanggal Pinjam</td>
                <td>: {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>Rencana Kembali</td>
                <td>:
                    {{ $peminjaman->tanggal_kembali_rencana
                        ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d-m-Y')
                        : '-' }}
                </td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: {{ strtoupper($peminjaman->status) }}</td>
            </tr>
        </table>

        @if($peminjaman->tujuan)
            <hr class="border-black mb-3">
            <p class="font-semibold">Tujuan</p>
            <p class="text-sm">{{ $peminjaman->tujuan }}</p>
        @endif

        <hr class="border-black my-3">

        <div class="text-center">
            {!! $qrCode !!}
            <p class="text-xs mt-1">QR Code Peminjaman</p>
        </div>

        <hr class="border-black mt-3">

        <p class="text-center text-xs mt-2">
            Struk ini adalah bukti peminjaman yang sah
        </p>
    </div>
</div>

<!-- ================= CSS PRINT FIX ================= -->
<style>
@media print {
    body {
        visibility: hidden;
    }

    #struk, #struk * {
        visibility: visible;
    }

    #struk {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none;
    }

    .no-print {
        display: none;
    }
}
</style>
@endsection

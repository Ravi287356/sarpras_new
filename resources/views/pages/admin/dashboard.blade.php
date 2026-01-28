@extends('layouts.app')

@section('content')
<div class="max-w-5xl">
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <div class="text-lg font-semibold">Selamat datang di sistem Sarpras!!</div>
        <div class="text-slate-300 text-sm mt-1">Ringkasan data untuk admin.</div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                <div class="text-slate-300 text-sm">Total User</div>
                <div class="text-2xl font-bold mt-2">{{ $totalUsers ?? 0 }}</div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                <div class="text-slate-300 text-sm">Kategori Sarpras</div>
                <div class="text-2xl font-bold mt-2">{{ $totalKategori ?? 0 }}</div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-5">
                <div class="text-slate-300 text-sm">Barang Masuk</div>
                <div class="text-2xl font-bold mt-2">--</div>
            </div>
        </div>
    </div>
</div>
@endsection

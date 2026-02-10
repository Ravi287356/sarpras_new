@extends('layouts.app')

@section('content')
    <div class="max-w-3xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight">Ajukan Peminjaman</h1>
            <p class="text-slate-300 text-sm mt-1">Isi data peminjaman dengan benar.</p>
        </div>

        @if (session('error'))
            <div class="mb-4 rounded-xl bg-rose-500/10 text-rose-200 ring-1 ring-rose-500/30 px-4 py-3">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
            @if($isWeekend)
                <div class="mb-6 p-4 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-200 text-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-1"></i>
                    <div>
                        <span class="font-bold block uppercase tracking-wide text-xs mb-1">Layanan Libur</span>
                        Peminjaman tidak tersedia pada hari Sabtu dan Minggu. Terimakasih.
                    </div>
                </div>
            @endif

            <div class="mb-4 text-slate-200">
                <div class="text-lg font-semibold">{{ $sarpras->nama }}</div>
                <div class="text-sm text-slate-300 mt-1">
                    Kode SAMPLE: <span class="text-slate-100">{{ $sarpras->sample_item?->kode ?? '-' }}</span> •
                    Kategori: <span class="text-slate-100">{{ $sarpras->kategori?->nama ?? '-' }}</span> •
                    Lokasi: <span class="text-slate-100">{{ $sarpras->sample_item?->lokasi?->nama ?? '-' }}</span>
                </div>
                <div class="text-sm text-slate-300 mt-1">
                    Kondisi yang dipilih: <span class="text-slate-100 font-bold uppercase">{{ $sarpras->selected_kondisi_nama }}</span>
                </div>
                <div class="text-sm text-slate-300 mt-1">
                    Stok tersedia (kondisi ini): <span class="text-slate-100 font-semibold">{{ (int) $sarpras->jumlah_stok }}</span>
                </div>
            </div>

            <form action="{{ route('user.peminjaman.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="sarpras_id" value="{{ $sarpras->id }}">
                <input type="hidden" name="kondisi_alat_id" value="{{ $sarpras->selected_kondisi_id }}">
                <input type="hidden" name="status_peminjaman_id" value="{{ $sarpras->selected_status_id }}">

                <div>
                    <label class="text-sm text-slate-200">Jumlah</label>
                    <input type="number" name="jumlah" min="1" max="{{ (int) $sarpras->jumlah_stok }}"
                        value="{{ old('jumlah', 1) }}" {{ $isWeekend ? 'disabled' : '' }}
                        class="mt-2 w-full rounded-xl bg-slate-950/40 border border-white/10 px-4 py-3 text-slate-100 outline-none disabled:opacity-50">
                    @error('jumlah')
                        <div class="text-rose-300 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-slate-200">Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                            {{ $isWeekend ? 'disabled' : '' }}
                            class="mt-2 w-full rounded-xl bg-slate-950/40 border border-white/10 px-4 py-3 text-slate-100 outline-none disabled:opacity-50">
                        @error('tanggal_pinjam')
                            <div class="text-rose-300 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm text-slate-200">Estimasi Tanggal Kembali</label>
                        <input type="date" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana') }}"
                            {{ $isWeekend ? 'disabled' : '' }}
                            class="mt-2 w-full rounded-xl bg-slate-950/40 border border-white/10 px-4 py-3 text-slate-100 outline-none disabled:opacity-50">
                        @error('tanggal_kembali_rencana')
                            <div class="text-rose-300 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="text-sm text-slate-200">Tujuan Peminjaman</label>
                    <textarea name="tujuan" rows="3" required {{ $isWeekend ? 'disabled' : '' }}
                        class="mt-2 w-full rounded-xl bg-slate-950/40 border border-white/10 px-4 py-3 text-slate-100 outline-none disabled:opacity-50"
                        placeholder="Contoh: Digunakan untuk kegiatan praktik lab">{{ old('tujuan') }}</textarea>
                    @error('tujuan')
                        <div class="text-rose-300 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>


                <div class="flex gap-2 pt-2">
                    <a href="{{ route('user.peminjaman.available') }}"
                        class="px-4 py-3 rounded-xl border border-white/10 text-slate-200 hover:bg-white/5 transition">
                        Kembali
                    </a>

                    <button type="submit"
                        {{ $isWeekend ? 'disabled' : '' }}
                        class="px-4 py-3 rounded-xl bg-emerald-500/10 text-emerald-200 ring-1 ring-emerald-500/30 hover:bg-emerald-500/15 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Kirim Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    <a href="{{ route(auth()->user()->role->nama === 'admin'
            ? 'admin.pengaduan.index'
            : 'operator.pengaduan.index') }}"
       class="text-emerald-400 text-sm">← Kembali</a>

    <h1 class="text-2xl font-semibold text-white mt-3">📝 Tanggapi Pengaduan</h1>

    <!-- Detail -->
    <div class="mt-6 rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 p-6 space-y-3">
        <p><b>Judul:</b> {{ $pengaduan->judul }}</p>
        <p><b>User:</b> {{ $pengaduan->user->username ?? '-' }}</p>
        <p><b>Lokasi:</b> {{ $pengaduan->lokasi->nama ?? '-' }}</p>
        <p><b>Status:</b> {{ $pengaduan->status }}</p>
        <p><b>Tanggal:</b> {{ $pengaduan->created_at->format('d-m-Y H:i') }}</p>
    </div>

    <!-- Riwayat Tanggapan -->
    @if($pengaduan->catatanPengaduan->count())
    <div class="mt-6 space-y-3">
        <h3 class="text-slate-300 font-semibold">Riwayat Tanggapan</h3>
        @foreach($pengaduan->catatanPengaduan as $catatan)
            <div class="rounded-lg bg-slate-950/60 p-4">
                <div class="flex justify-between text-sm text-slate-400">
                    <span>{{ $catatan->user->username }}</span>
                    <span>{{ $catatan->created_at->format('d-m-Y H:i') }}</span>
                </div>
                <p class="mt-2 text-slate-200">{{ $catatan->catatan }}</p>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Form Tanggapan -->
    <form method="POST"
          action="{{ route(auth()->user()->role->nama === 'admin'
                ? 'admin.pengaduan.updateStatus'
                : 'operator.pengaduan.updateStatus', $pengaduan->id) }}"
          class="mt-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="text-slate-300 text-sm">Status</label>
            <select name="status" class="w-full mt-1 rounded-lg bg-slate-950 text-white ring-1 ring-slate-700">
                @foreach(['Belum Ditindaklanjuti','Sedang Diproses','Selesai','Ditutup'] as $st)
                    <option value="{{ $st }}" {{ $pengaduan->status == $st ? 'selected' : '' }}>
                        {{ $st }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-slate-300 text-sm">Catatan</label>
            <textarea name="catatan" rows="5"
                class="w-full mt-1 rounded-lg bg-slate-950 text-white ring-1 ring-slate-700"
                placeholder="Tulis tanggapan baru..."></textarea>
        </div>

        <button class="px-6 py-3 rounded-xl bg-emerald-600/20 ring-1 ring-emerald-500/30">
            Simpan Tanggapan
        </button>
    </form>

</div>
@endsection

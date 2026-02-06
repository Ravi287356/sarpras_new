@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <h1 class="text-2xl font-semibold tracking-tight text-white">
        📋 Ajukan Pengaduan
    </h1>
    <p class="text-slate-400 text-sm mt-1">
        Laporkan masalah atau kerusakan pada sarana prasarana
    </p>

    <!-- Error -->
    @if ($errors->any())
        <div class="mt-5 rounded-xl bg-red-500/10 ring-1 ring-red-500/30 p-4 text-red-200 text-sm">
            <b>Gagal:</b>
            <ul class="list-disc list-inside mt-2 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('user.pengaduan.store') }}"
          method="POST"
          class="mt-6 rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 p-6 space-y-6">
        @csrf

        <!-- Judul -->
        <div>
            <label class="text-sm text-slate-300">Judul Pengaduan</label>
            <input type="text"
                   name="judul"
                   value="{{ old('judul') }}"
                   required
                   placeholder="Contoh: Proyektor ruang 101 rusak"
                   class="mt-2 w-full rounded-xl bg-slate-950/60 text-white
                          ring-1 ring-slate-800 px-4 py-3
                          placeholder:text-slate-500
                          focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
        </div>

        <!-- Deskripsi -->
        <div>
            <label class="text-sm text-slate-300">Deskripsi Masalah</label>
            <textarea name="deskripsi"
                      rows="5"
                      required
                      placeholder="Jelaskan masalah secara detail"
                      class="mt-2 w-full rounded-xl bg-slate-950/60 text-white
                             ring-1 ring-slate-800 px-4 py-3
                             placeholder:text-slate-500
                             focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">{{ old('deskripsi') }}</textarea>
        </div>

        <!-- Lokasi -->
        <div>
                <label class="text-sm text-slate-300">Lokasi Barang</label>

                <!-- Optional select dari daftar lokasi yang ada -->
                @if(isset($lokasis) && $lokasis->count())
                    <select name="lokasi_id" class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3" required>
                        <option value="">Pilih lokasi dari daftar</option>
                        @foreach($lokasis as $l)
                            <option value="{{ $l->id }}" {{ old('lokasi_id') == $l->id ? 'selected' : '' }}>{{ $l->nama }}</option>
                        @endforeach
                    </select>
                @endif

        </div>

        <!-- Kategori (opsional) -->
        <div>
            <label class="text-sm text-slate-300">Kategori Sarpras</label>
            @if(isset($kategoris) && $kategoris->count())
                <select name="kategori_id" class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3">
                    <option value="">Pilih kategori (opsional)</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <!-- Action -->
        <div class="flex items-center gap-3 pt-4 border-t border-slate-800">
            <button type="submit"
                    class="px-5 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30
                           text-emerald-200 ring-1 ring-emerald-500/30
                           transition font-medium">
                <i class="bi bi-send"></i> Kirim Pengaduan
            </button>

            <a href="{{ route('user.pengaduan.riwayat') }}"
               class="px-5 py-3 rounded-xl bg-slate-800/40 hover:bg-slate-800/60
                      ring-1 ring-slate-700 text-slate-100 transition">
                Batal
            </a>
        </div>
    </form>

    <!-- Info -->
    <div class="mt-6 rounded-xl bg-emerald-500/10 ring-1 ring-emerald-500/20
                p-4 text-emerald-200 text-sm">
        💡 Isi pengaduan dengan jelas agar dapat ditindaklanjuti dengan cepat
    </div>

</div>
@endsection

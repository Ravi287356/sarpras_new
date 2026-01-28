@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold tracking-tight">Edit Sarpras</h1>
    <p class="text-slate-400 text-sm mt-1">Perbarui data sarpras.</p>

    @if ($errors->any())
        <div class="mt-5 rounded-xl bg-red-500/10 ring-1 ring-red-500/30 p-4 text-red-200 text-sm">
            <b>Gagal:</b>
            <ul class="list-disc list-inside mt-2 space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.sarpras.update', $sarpras->id) }}" method="POST"
          class="mt-6 rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-slate-300">Kode</label>
                <input type="text" name="kode" value="{{ old('kode', $sarpras->kode) }}" required
                       class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
            </div>

            <div>
                <label class="text-sm text-slate-300">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $sarpras->nama) }}" required
                       class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-slate-300">Kategori</label>
                <select name="kategori_id" required
                        class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                               focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" @selected(old('kategori_id', $sarpras->kategori_id) == $k->id)>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm text-slate-300">Lokasi</label>
                <select name="lokasi_id" required
                        class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                               focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
                    @foreach($lokasis as $l)
                        <option value="{{ $l->id }}" @selected(old('lokasi_id', $sarpras->lokasi_id) == $l->id)>
                            {{ $l->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-slate-300">Jumlah Stok</label>
                <input type="number" name="jumlah_stok" min="0" value="{{ old('jumlah_stok', $sarpras->jumlah_stok) }}" required
                       class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
            </div>

            <div>
                <label class="text-sm text-slate-300">Kondisi Saat Ini (opsional)</label>
                <input type="text" name="kondisi_saat_ini" value="{{ old('kondisi_saat_ini', $sarpras->kondisi_saat_ini) }}"
                       class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/25
                           text-emerald-200 ring-1 ring-emerald-500/30 transition font-medium">
                Simpan Perubahan
            </button>

            <a href="{{ route('admin.sarpras.index') }}"
               class="px-5 py-3 rounded-xl bg-slate-800/40 hover:bg-slate-800/60
                      ring-1 ring-slate-700 text-slate-100 transition">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection

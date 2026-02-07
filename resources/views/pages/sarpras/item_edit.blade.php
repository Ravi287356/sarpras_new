@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold tracking-tight">Edit Item: {{ $item->kode }}</h1>
    <p class="text-slate-400 text-sm mt-1">Perbarui data item sarpras</p>

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

    <form action="{{ route('admin.sarpras_item.update', $item->id) }}" method="POST"
          class="mt-6 rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 p-6 space-y-5">
        @csrf
        @method('PUT')

        {{-- KODE --}}
        <div>
            <label class="text-sm text-slate-300">Kode Item</label>
            <input type="text" name="kode" value="{{ old('kode', $item->kode) }}" required
                   class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                          focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
        </div>

        {{-- LOKASI --}}
        <div>
            <label class="text-sm text-slate-300">Lokasi Penyimpanan</label>
            <select name="lokasi_id" required
                    class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                           focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
                @foreach($lokasis as $l)
                    <option value="{{ $l->id }}" @selected(old('lokasi_id', $item->lokasi_id) == $l->id)>
                        {{ $l->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- KONDISI --}}
        <div>
            <label class="text-sm text-slate-300">Kondisi Alat (Opsional)</label>
            <select name="kondisi_alat_id"
                    class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                           focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
                <option value="">-- Pilih Kondisi --</option>
                @foreach($kondisis as $k)
                    <option value="{{ $k->id }}" @selected(old('kondisi_alat_id', $item->kondisi_alat_id) == $k->id)>
                        {{ $k->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- STATUS PEMINJAMAN --}}
        <div>
            <label class="text-sm text-slate-300">Status Peminjaman (Opsional)</label>
            <select name="status_peminjaman_id"
                    class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                           focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
                <option value="">-- Pilih Status --</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->id }}" @selected(old('status_peminjaman_id', $item->status_peminjaman_id) == $s->id)>
                        {{ $s->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-800">
            <button type="submit"
                    class="px-5 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30
                           text-emerald-200 ring-1 ring-emerald-500/30 transition font-medium">
                Simpan Perubahan
            </button>

            <a href="{{ route('admin.sarpras.items', $sarpras->id) }}"
               class="px-5 py-3 rounded-xl bg-slate-800/40 hover:bg-slate-800/60
                      ring-1 ring-slate-700 text-slate-100 transition">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection

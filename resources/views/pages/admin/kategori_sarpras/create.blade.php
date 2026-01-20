@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Tambah Kategori</h1>
            <p class="text-slate-400 text-sm mt-1">Isi data kategori sarpras.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-xl bg-red-500/10 ring-1 ring-red-500/30 p-4 text-red-200 text-sm">
            <b>Gagal:</b>
            <ul class="list-disc list-inside mt-2 space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.kategori_sarpras.store') }}" method="POST"
        class="rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 p-6 space-y-5">
        @csrf

        <div>
            <label class="text-sm text-slate-300">Nama</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required
                class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                       placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
        </div>

        <div>
            <label class="text-sm text-slate-300">Deskripsi</label>
            <textarea name="deskripsi" rows="4"
                class="mt-2 w-full rounded-xl bg-slate-950/60 text-white ring-1 ring-slate-800 px-4 py-3
                       placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit"
                class="px-5 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/25
                       text-emerald-200 ring-1 ring-emerald-500/30 transition font-medium">
                Simpan
            </button>

            <a href="{{ route('admin.kategori_sarpras.index') }}"
                class="px-5 py-3 rounded-xl bg-slate-800/40 hover:bg-slate-800/60
                       ring-1 ring-slate-700 text-slate-100 transition">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection

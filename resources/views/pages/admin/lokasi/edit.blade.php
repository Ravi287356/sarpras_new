@extends('layouts.app')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-semibold">Edit Lokasi</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-rose-500/40 bg-rose-500/10 p-4 text-rose-200 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.lokasi.update', $lokasi->id) }}" method="POST"
          class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="text-sm text-slate-300">Nama Lokasi</label>
            <input type="text" name="nama" value="{{ old('nama', $lokasi->nama) }}" required
                   class="mt-2 w-full rounded-xl bg-slate-950/60 border border-white/10 px-4 py-3">
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-400/30 text-emerald-200">
                Simpan
            </button>
            <a href="{{ route('admin.lokasi.index') }}"
               class="px-4 py-3 rounded-xl border border-white/10 text-slate-100">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection

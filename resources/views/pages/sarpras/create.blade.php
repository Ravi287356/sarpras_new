@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <h1 class="text-2xl font-semibold tracking-tight">Tambah Sarpras</h1>
    <p class="text-slate-400 text-sm mt-1">Isi data sarpras</p>

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

    <form action="{{ route('admin.sarpras.store') }}" method="POST"
          class="mt-6 rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 p-6 space-y-6"
          id="formSarpras">
        @csrf

        {{-- KODE & NAMA --}}
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-slate-300">Kode (Auto-Generate)</label>
                <div id="kodeDisplay"
                     class="mt-2 w-full rounded-xl bg-slate-950/60 text-slate-400
                            ring-1 ring-slate-800 px-4 py-3 min-h-[48px] flex items-center">
                    <span class="text-slate-500 text-sm">
                        Pilih kategori, lokasi, dan ketik nama
                    </span>
                </div>
            </div>

            <div>
                <label class="text-sm text-slate-300">Nama</label>
                <input type="text" name="nama" id="inputNama"
                       value="{{ old('nama') }}" required
                       class="mt-2 w-full rounded-xl bg-slate-950/60 text-white
                              ring-1 ring-slate-800 px-4 py-3
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
            </div>
        </div>

        {{-- KATEGORI & LOKASI --}}
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-slate-300">Kategori</label>
                <select name="kategori_id" id="selectKategori" required
                        class="mt-2 w-full rounded-xl bg-slate-950/60 text-white
                               ring-1 ring-slate-800 px-4 py-3
                               focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" @selected(old('kategori_id') == $k->id)>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm text-slate-300">Lokasi</label>
                <select name="lokasi_id" id="selectLokasi" required
                        class="mt-2 w-full rounded-xl bg-slate-950/60 text-white
                               ring-1 ring-slate-800 px-4 py-3
                               focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($lokasis as $l)
                        <option value="{{ $l->id }}" @selected(old('lokasi_id') == $l->id)>
                            {{ $l->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- STOK & KONDISI --}}
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-slate-300">Jumlah Stok</label>
                <input type="number" name="jumlah_stok" min="0"
                       value="{{ old('jumlah_stok', 0) }}" required
                       class="mt-2 w-full rounded-xl bg-slate-950/60 text-white
                              ring-1 ring-slate-800 px-4 py-3
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
            </div>

            <div>
                <label class="text-sm text-slate-300">Kondisi Saat Ini (opsional)</label>
                <input type="text" name="kondisi_saat_ini"
                       value="{{ old('kondisi_saat_ini') }}"
                       class="mt-2 w-full rounded-xl bg-slate-950/60 text-white
                              ring-1 ring-slate-800 px-4 py-3
                              focus:outline-none focus:ring-2 focus:ring-emerald-500/60 transition">
            </div>
        </div>

        {{-- BUTTON --}}
        <div class="flex items-center gap-3 pt-4 border-t border-slate-800">
            <button type="submit"
                    class="px-5 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30
                           text-emerald-200 ring-1 ring-emerald-500/30 transition font-medium">
                Simpan
            </button>

            <a href="{{ route('admin.sarpras.index') }}"
               class="px-5 py-3 rounded-xl bg-slate-800/40 hover:bg-slate-800/60
                      ring-1 ring-slate-700 text-slate-100 transition">
                Kembali
            </a>
        </div>
    </form>
</div>

{{-- SCRIPT AUTO GENERATE KODE --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const kategori = document.getElementById('selectKategori');
    const lokasi   = document.getElementById('selectLokasi');
    const nama     = document.getElementById('inputNama');
    const kodeBox  = document.getElementById('kodeDisplay');

    async function generateCode() {
        if (!kategori.value || !lokasi.value || !nama.value.trim()) {
            kodeBox.innerHTML =
                '<span class="text-slate-500 text-sm">Pilih kategori, lokasi, dan ketik nama</span>';
            return;
        }

        const res = await fetch('{{ route("admin.sarpras.generate-code") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector("input[name=_token]").value
            },
            body: JSON.stringify({
                kategori_id: kategori.value,
                lokasi_id: lokasi.value,
                nama: nama.value
            })
        });

        const data = await res.json();

        kodeBox.innerHTML = data.success
            ? `<span class="text-emerald-300 font-semibold text-lg">${data.code}</span>`
            : `<span class="text-red-300 text-sm">Gagal generate kode</span>`;
    }

    kategori.addEventListener('change', generateCode);
    lokasi.addEventListener('change', generateCode);
    nama.addEventListener('input', generateCode);

    if (kategori.value && lokasi.value && nama.value) generateCode();
});
</script>
@endsection

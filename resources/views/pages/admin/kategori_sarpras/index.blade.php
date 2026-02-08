@extends('layouts.app')

@section('content')
<div class="max-w-5xl">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Sarpras</h1>
        </div>

        <a href="{{ route('admin.kategori_sarpras.create') }}"
           class="px-4 py-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/25
                  text-emerald-200 ring-1 ring-emerald-500/30 transition font-medium">
            + Tambah Kategori
        </a>
    </div>


    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-950/40 text-slate-200">
                <tr class="text-left">
                    <th class="px-5 py-4 w-16">#</th>
                    <th class="px-5 py-4">Nama</th>
                    <th class="px-5 py-4">Deskripsi</th>
                    <th class="px-5 py-4 w-48">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800">
                @forelse($kategoris as $i => $k)
                    <tr class="hover:bg-slate-950/30">
                        <td class="px-5 py-4 text-slate-300">{{ $i+1 }}</td>
                        <td class="px-5 py-4 font-medium">{{ $k->nama }}</td>
                        <td class="px-5 py-4 text-slate-300">{{ $k->deskripsi ?? '-' }}</td>
                        <td class="px-5 py-4 flex items-center gap-2">
                            <a href="{{ route('admin.kategori_sarpras.edit', $k->id) }}"
                               class="px-3 py-2 rounded-xl bg-slate-800/40 hover:bg-slate-800/60
                                      ring-1 ring-slate-700 text-slate-100 transition">
                                Edit
                            </a>

                            <form action="{{ route('admin.kategori_sarpras.destroy', $k->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="px-3 py-2 rounded-xl bg-red-600/15 hover:bg-red-600/25
                                           ring-1 ring-red-500/30 text-red-200 transition">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-slate-400">
                            Belum ada kategori.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

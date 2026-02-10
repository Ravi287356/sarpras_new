@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Data Sarpras</h1>
            <p class="text-slate-400 text-sm mt-1">Kelola data sarpras</p>
        </div>

        @if (auth()->user()->role->nama === 'admin')
            <div class="flex gap-2">
                <a href="{{ route('admin.sarpras_item.trashed') }}"
                   class="px-5 py-3 rounded-xl bg-amber-600/20 hover:bg-amber-600/25
                          text-amber-200 ring-1 ring-amber-500/30 transition font-medium">
                    Item Terhapus
                </a>

                <a href="{{ route('admin.sarpras.create') }}"
                   class="px-5 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/25
                          text-emerald-200 ring-1 ring-emerald-500/30 transition font-medium">
                    + Tambah Sarpras
                </a>
            </div>
        @endif
    </div>


    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-slate-300 bg-white/5">
                    <tr>
                        <th class="text-left px-5 py-4">No</th>
                        <th class="text-left px-5 py-4">Nama</th>
                        <th class="text-left px-5 py-4">Kategori</th>
                        <th class="text-left px-5 py-4">Jumlah Item</th>
                        <th class="text-left px-5 py-4">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $i => $row)
                        <tr class="text-slate-200">
                            <td class="px-5 py-4">{{ $i+1 }}</td>
                            <td class="px-5 py-4 font-medium">{{ $row->nama }}</td>
                            <td class="px-5 py-4">{{ $row->kategori?->nama ?? '-' }}</td>
                            <td class="px-5 py-4">{{ $row->items->count() ?? 0 }}</td>
                             <td class="px-5 py-4 flex gap-2">
                                <a href="{{ route(auth()->user()->role->nama . '.sarpras.items', $row->id) }}"
                                   class="px-4 py-2 rounded-xl ring-1 ring-white/10 hover:bg-white/5 transition">
                                    {{ auth()->user()->role->nama === 'admin' ? 'Inventory' : 'Lihat Selengkapnya' }}
                                </a>

                                @if (auth()->user()->role->nama === 'admin')
                                    <a href="{{ route('admin.sarpras.edit', $row->id) }}"
                                       class="px-4 py-2 rounded-xl bg-blue-600/20 hover:bg-blue-600/25
                                              text-blue-200 ring-1 ring-blue-500/30 transition">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.sarpras.destroy', $row->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus sarpras ini? Semua item di dalamnya akan ikut terhapus.')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-4 py-2 rounded-xl bg-red-600/20 hover:bg-red-600/25
                                                       text-red-200 ring-1 ring-red-500/30 transition">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                Belum ada data sarpras.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection

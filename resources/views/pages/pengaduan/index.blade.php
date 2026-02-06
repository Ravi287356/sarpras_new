@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">🛠️ Data Pengaduan</h1>
            <p class="text-slate-400 text-sm mt-1">
                Kelola dan tindak lanjuti pengaduan pengguna
            </p>
        </div>

        <!-- Filter -->
        <form method="GET" class="flex gap-2">
            <select name="lokasi_id" class="px-3 py-2 rounded-lg bg-slate-950 text-white ring-1 ring-slate-700">
                <option value="">Semua Lokasi</option>
                @foreach($lokasi as $lok)
                    <option value="{{ $lok->id }}" {{ request('lokasi_id') == $lok->id ? 'selected' : '' }}>
                        {{ $lok->nama }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-2 rounded-lg bg-slate-950 text-white ring-1 ring-slate-700">
                <option value="">Semua Status</option>
                @foreach(['Belum Ditindaklanjuti','Sedang Diproses','Selesai','Ditutup'] as $st)
                    <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                        {{ $st }}
                    </option>
                @endforeach
            </select>

            <button class="px-4 py-2 bg-emerald-600/20 ring-1 ring-emerald-500/30 rounded-lg">
                Filter
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-slate-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-800/50 text-slate-300">
                <tr>
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Judul</th>
                    <th class="px-5 py-3">Lokasi</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800">
                @forelse($pengaduan as $item)
                <tr>
                    <td class="px-5 py-3">{{ $item->user->username ?? '-' }}</td>
                    <td class="px-5 py-3 text-white font-medium">{{ $item->judul }}</td>
                    <td class="px-5 py-3">{{ $item->lokasi->nama ?? '-' }}</td>
                    <td class="px-5 py-3">{{ $item->status }}</td>
                    <td class="px-5 py-3 text-slate-400">
                        {{ $item->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ auth()->user()->role->nama === 'admin'
                            ? route('admin.pengaduan.respond', $item->id)
                            : route('operator.pengaduan.respond', $item->id) }}"
                           class="px-3 py-2 rounded-lg bg-emerald-600/20 ring-1 ring-emerald-500/30 text-sm">
                            Tanggapi
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-slate-500">
                        Data kosong
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $pengaduan->withQueryString()->links() }}
    </div>
</div>
@endsection

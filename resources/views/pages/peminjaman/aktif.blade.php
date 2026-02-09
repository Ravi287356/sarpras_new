@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold">Peminjaman Aktif</h1>
        
        <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <input type="text" name="user_search" value="{{ request('user_search') }}" 
                       placeholder="Cari user..." 
                       class="w-48 rounded-xl bg-slate-900/60 border border-white/10 px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500/50">
            </div>
            <div class="relative">
                <input type="text" name="alat_search" value="{{ request('alat_search') }}" 
                       placeholder="Cari alat..." 
                       class="w-48 rounded-xl bg-slate-900/60 border border-white/10 px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500/50">
            </div>
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm transition font-medium">
                Cari
            </button>
            @if(request('user_search') || request('alat_search'))
                <a href="{{ url()->current() }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-400/30 bg-emerald-500/10 p-3 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-400/30 bg-red-500/10 p-3 text-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-white/5">
                <tr>
                    <th class="px-4 py-3 text-left">Disetujui</th>
                    <th class="px-4 py-3 text-left">User</th>
                    <th class="px-4 py-3 text-left">Sarpras</th>
                    <th class="px-4 py-3 text-center">Jumlah</th>
                    <th class="px-4 py-3 text-left">Tgl Pinjam</th>
                    <th class="px-4 py-3 text-left">Est. Kembali</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-white/10">
                @forelse ($items as $row)
                    <tr>
                        <td class="px-4 py-3">
                            {{ $row->approved_at ? \Carbon\Carbon::parse($row->approved_at)->format('d-m-Y H:i') : '-' }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $row->user?->username ?? '-' }}</div>
                            <div class="text-xs text-slate-300">{{ $row->user?->role?->nama ?? '-' }}</div>
                        </td>

                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $row->sarpras?->nama ?? '-' }}</div>
                            <div class="text-xs text-slate-300">
                                {{ $row->sarpras?->kategori?->nama ?? '-' }}
                            </div>
                        </td>

                        <td class="px-4 py-3 text-center">{{ $row->jumlah }}</td>

                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($row->tanggal_pinjam)->format('d-m-Y') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $row->tanggal_kembali_rencana ? \Carbon\Carbon::parse($row->tanggal_kembali_rencana)->format('d-m-Y') : '-' }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            <div class="flex flex-col gap-2">
                                <a href="{{ route((auth()->user()->role->nama === 'admin' ? 'admin' : 'operator').'.peminjaman.struk', $row->id) }}"
                                   class="px-3 py-2 rounded-xl border border-blue-400/50 bg-blue-500/10 hover:bg-blue-500/20 transition text-blue-300 text-xs">
                                    📄 Struk
                                </a>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-300">
                            Belum ada peminjaman aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

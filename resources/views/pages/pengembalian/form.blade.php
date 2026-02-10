@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto pb-12">
        <a href="{{ route('pengembalian.index') }}" class="inline-flex items-center text-sm text-slate-400 hover:text-white mb-6">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Pencarian
        </a>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Left Panel: Information -->
            <div class="md:col-span-1 space-y-6">
                <!-- Main Info -->
                <div class="bg-slate-900 border border-white/10 rounded-xl p-5">
                    <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Informasi Pinjaman</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs text-slate-500 mb-1">Kode Pinjam</div>
                            <div class="text-lg font-mono font-bold text-blue-400">{{ $peminjaman->kode_peminjaman }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500 mb-1">Peminjam</div>
                            <div class="text-white font-medium">{{ $peminjaman->user->username }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                             <div>
                                <div class="text-xs text-slate-500 mb-1">Tanggal Pinjam</div>
                                <div class="text-sm text-white">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 mb-1">Jadwal Kembali</div>
                                <div class="text-sm text-white">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items List -->
                <div class="bg-slate-900 border border-white/10 rounded-xl p-5">
                     <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Barang ({{\count($peminjaman->items)}})</h2>
                     <ul class="space-y-3">
                        @foreach ($peminjaman->items as $item)
                        <li class="bg-slate-800/50 p-3 rounded-lg border border-white/5">
                            <div class="text-white font-medium text-sm">{{ $item->sarprasItem->sarpras->nama ?? '-' }}</div>
                            <div class="text-xs text-slate-500 mt-1 font-mono">Kode: {{ $item->sarprasItem->kode }}</div>
                        </li>
                        @endforeach
                     </ul>
                </div>
            </div>

            <!-- Right Panel: Form -->
            <div class="md:col-span-2">
                <div class="bg-slate-900 border border-white/10 rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-slate-800 px-6 py-4 border-b border-white/5">
                        <h1 class="text-lg font-bold text-white">Form Pengembalian</h1>
                        <p class="text-sm text-slate-400">Lengkapi formulir di bawah ini untuk menyelesaikan pengembalian.</p>
                    </div>
                    
                    <div class="p-6">
                        @if($isWeekend)
                            <div class="mb-6 p-4 rounded-xl bg-orange-500/10 border border-orange-500/20 text-orange-200 text-sm flex items-start gap-3">
                                <i class="fa-solid fa-circle-exclamation mt-1"></i>
                                <div>
                                    <span class="font-bold block uppercase tracking-wide text-xs mb-1">Layanan Libur</span>
                                    Pengembalian tidak tersedia pada hari Sabtu dan Minggu. Terimakasih.
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 p-4 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm">
                                <ul class="list-disc pl-4 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('pengembalian.store') }}" enctype="multipart/form-data" class="space-y-8">
                            @csrf
                            <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">

                            <!-- Section: Per Item Information -->
                            <div class="space-y-6">
                                <h3 class="text-white font-semibold flex items-center gap-2">
                                    <span class="w-1 h-6 bg-blue-500 rounded-full"></span>
                                    Detail Kondisi per Barang
                                </h3>

                                <div class="space-y-4">
                                    @foreach ($peminjaman->items as $item)
                                        <div class="p-5 bg-slate-950/50 border border-white/5 rounded-xl space-y-4">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="text-white font-medium">{{ $item->sarprasItem->sarpras->nama ?? '-' }}</div>
                                                    <div class="text-xs text-blue-400 font-mono mt-0.5">{{ $item->sarprasItem->kode }}</div>
                                                </div>
                                                <input type="hidden" name="items[{{ $item->sarprasItem->id }}][sarpras_item_id]" value="{{ $item->sarprasItem->id }}">
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <!-- Kondisi per Item -->
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Kondisi Barang</label>
                                                    <div class="relative">
                                                        <select name="items[{{ $item->sarprasItem->id }}][kondisi_alat_id]" required {{ $isWeekend ? 'disabled' : '' }}
                                                            class="w-full pl-3 pr-8 py-2.5 bg-slate-900 border border-white/10 rounded-lg text-white appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm cursor-pointer disabled:opacity-50">
                                                            <option value="">-- Pilih Kondisi --</option>
                                                            @foreach ($kondisiAlat as $kondisi)
                                                                <option value="{{ $kondisi->id }}">{{ $kondisi->nama }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500">
                                                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Foto per Item -->
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Foto Bukti (Opsional)</label>
                                                    <input type="file" name="items[{{ $item->sarprasItem->id }}][foto]" accept="image/*" {{ $isWeekend ? 'disabled' : '' }}
                                                        class="w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer disabled:opacity-50"/>
                                                </div>
                                            </div>

                                            <!-- Deskripsi Kerusakan per Item -->
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Deskripsi Kerusakan (Jika Ada)</label>
                                                <textarea name="items[{{ $item->sarprasItem->id }}][deskripsi_kerusakan]" rows="2" {{ $isWeekend ? 'disabled' : '' }}
                                                    class="w-full px-3 py-2 bg-slate-900 border border-white/10 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:outline-none placeholder-slate-700 text-sm disabled:opacity-50" 
                                                    placeholder="Jelaskan detail kerusakan barang ini..."></textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr class="border-white/5">

                            <!-- Section: General Notes -->
                            <div class="space-y-4">
                                <h3 class="text-white font-semibold flex items-center gap-2">
                                    <span class="w-1 h-6 bg-blue-500 rounded-full"></span>
                                    Catatan Umum
                                </h3>
                                
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase">Catatan Petugas <span class="text-rose-500">*</span></label>
                                        <textarea name="catatan_petugas" required rows="2" {{ $isWeekend ? 'disabled' : '' }}
                                            class="w-full px-4 py-3 bg-slate-950 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none placeholder-slate-700 text-sm disabled:opacity-50" 
                                            placeholder="Tambahkan catatan penyelesaian..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-white/10 mt-6 mb-6">

                            <button type="submit" 
                                {{ $isWeekend ? 'disabled' : '' }}
                                class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg transition shadow-lg text-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fa-solid fa-check-circle mr-2"></i> Konfirmasi Pengembalian
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Sarpras;
use App\Models\StatusPeminjaman;
use App\Models\PeminjamanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PeminjamanController extends Controller
{
    public function available()
    {
        $sarpras_list = Sarpras::with(['kategori', 'items'])
            ->whereNull('deleted_at')
            ->orderBy('nama', 'asc')
            ->get();

        $grouped_items = collect();

        foreach ($sarpras_list as $sarpras) {
             // Get available items grouped by condition AND status
             $availableGroups = $sarpras->items()
                ->available()
                ->with(['kondisi', 'lokasi', 'statusPeminjaman'])
                ->get()
                ->groupBy(function($item) {
                    return $item->kondisi_alat_id . '-' . ($item->status_peminjaman_id ?? 'null');
                });

            foreach ($availableGroups as $key => $itemsByGroup) {
                $firstItem = $itemsByGroup->first();
                
                // Clone sarpras to hold condition-specific info
                $group = clone $sarpras;
                $group->jumlah_stok = $itemsByGroup->count();
                $group->kondisi_saat_ini = $firstItem->kondisi?->nama ?? '-';
                $group->kondisi_id = $firstItem->kondisi_alat_id;
                $group->status_peminjaman_id = $firstItem->status_peminjaman_id;
                $group->lokasi_saat_ini = $firstItem->lokasi?->nama ?? '-';
                $group->sample_item = $firstItem;

                $grouped_items->push($group);
            }
        }

        // Sort: BUTUH MAINTENANCE at the top
        $grouped_items = $grouped_items->sortByDesc(function ($item) {
            return $item->sample_item?->getDisplayStatus() === 'BUTUH MAINTENANCE';
        });

        $items = $grouped_items->values();

        // ✅ sesuaikan dengan folder kamu (resources/views/pages/peminjaman/available.blade.php)
        return view('pages.peminjaman.available', [
            'title' => 'Sarpras Bisa Dipinjam',
            'items' => $items,
        ]);
    }

    public function create(string $sarpras_id, Request $request)
    {
        $sarpras = Sarpras::whereNull('deleted_at')->findOrFail($sarpras_id);
        $kondisi_id = $request->query('kondisi_id');
        $status_id = $request->query('status_id');

        // Calculate available items using scope and specific condition/status if provided
        $query = $sarpras->items()->available();
        
        if ($kondisi_id) {
            $query->where('kondisi_alat_id', $kondisi_id);
        }

        if ($status_id !== null && $status_id !== 'null') {
            $query->where('status_peminjaman_id', $status_id);
        } elseif ($status_id === 'null') {
            $query->whereNull('status_peminjaman_id');
        }

        $available_count = $query->count();
        $sarpras->jumlah_stok = $available_count;
        
        // Get sample item for location and condition display
        $sampleItem = (clone $query)->with(['kondisi', 'lokasi', 'statusPeminjaman'])->first();
        $sarpras->selected_kondisi_nama = $sampleItem?->kondisi?->nama ?? '-';
        $sarpras->selected_kondisi_id = $kondisi_id;
        $sarpras->selected_status_id = $status_id;
        $sarpras->sample_item = $sampleItem;

        // ✅ sesuaikan (resources/views/pages/peminjaman/create.blade.php)
        return view('pages.peminjaman.create', [
            'title' => 'Ajukan Peminjaman',
            'sarpras' => $sarpras,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sarpras_id' => ['required', 'exists:sarpras,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'kondisi_alat_id' => ['nullable', 'exists:kondisi_alat,id'],
            'status_peminjaman_id' => ['nullable'], // Dynamic check
            'tujuan' => ['nullable', 'string'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
        ]);

        DB::transaction(function () use ($request) {
            $sarpras = Sarpras::whereNull('deleted_at')->lockForUpdate()->findOrFail($request->sarpras_id);

            // Get exact available items using the scope and lock them
            $query = $sarpras->items()->available();
            
            if ($request->kondisi_alat_id) {
                $query->where('kondisi_alat_id', $request->kondisi_alat_id);
            }

            if ($request->status_peminjaman_id !== null && $request->status_peminjaman_id !== 'null') {
                $query->where('status_peminjaman_id', $request->status_peminjaman_id);
            } elseif ($request->status_peminjaman_id === 'null') {
                $query->whereNull('status_peminjaman_id');
            }

            $available_items = $query->lockForUpdate()->limit($request->jumlah)->get();

            if ($available_items->count() < $request->jumlah) {
                // Throw validation exception manually to rollback transaction
                throw \Illuminate\Validation\ValidationException::withMessages(['jumlah' => 'Stok tidak cukup atau sudah dipesan orang lain.']);
            }

            $peminjaman = Peminjaman::create([
                'user_id' => Auth::id(),
                'tujuan' => $request->tujuan,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            ]);

            $peminjaman->syncStatus('menunggu');

            // Immediately Link Items
            foreach ($available_items as $item) {
                PeminjamanItem::create([
                    'peminjaman_id' => $peminjaman->id,
                    'sarpras_item_id' => $item->id,
                ]);
            }
        });

        return redirect()->route('user.peminjaman.riwayat')->with('success', 'Peminjaman berhasil diajukan ✅');
    }

    public function riwayatUser()
    {
        // Fix: Use with('items.sarprasItem') to get sarpras info via pivot if needed, 
        // OR rely on the fact that Peminjaman usually groups items of same Sarpras? 
        // Current design allows mixing items in theory, but UI flow implies single Sarpras type per borrowing.
        // Let's load the items relation.
        
        $logs = Peminjaman::query()
            ->with(['items.sarprasItem.sarpras', 'items.sarprasItem.lokasi'])
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(10);

        // ✅ sesuai file kamu: resources/views/pages/peminjaman/riwayat.blade.php
        return view('pages.peminjaman.riwayat', [
            'title' => 'Riwayat Peminjaman',
            'logs' => $logs,
        ]);
    }

    public function riwayat()
    {
        $logs = Peminjaman::query()
            ->with([
                'user.role',
                'items.sarprasItem.sarpras.kategori', 
                'items.sarprasItem.lokasi',
                'approver'
            ])
            ->whereIn('status', ['disetujui', 'dikembalikan', 'ditolak']) // ✅ TERMASUK DITOLAK
            ->latest('approved_at')
            ->paginate(10);

        return view('pages.peminjaman.riwayat_semua', [
            'title' => 'Riwayat Peminjaman (Semua User)',
            'logs' => $logs,
        ]);
    }


    public function indexPermintaan()
    {
        $items = Peminjaman::query()
            ->with(['user.role', 'items.sarprasItem.sarpras.kategori'])
            ->where('status', 'menunggu')
            ->latest('created_at')
            ->paginate(20);

        // ✅ 1 view untuk admin & operator (sesuai struktur folder kamu)
        return view('pages.peminjaman.permintaan', [
            'title' => 'Permintaan Peminjaman',
            'items' => $items,
        ]);
    }

    public function indexAktif()
    {
        $items = Peminjaman::query()
            ->with(['user.role', 'items.sarprasItem.sarpras'])
            ->where('status', 'disetujui')
            ->latest('approved_at')
            ->get();

        $role = auth()->user()?->role?->nama;

        $view = $role === 'admin'
            ? 'pages.peminjaman.aktif'
            : 'pages.peminjaman.aktif';

        return view($view, [
            'title' => 'Peminjaman Aktif',
            'items' => $items,
        ]);
    }

    public function setujui(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'menunggu') {
            return back()->withErrors(['status' => 'Peminjaman ini sudah diproses.']);
        }

        DB::transaction(function () use ($peminjaman) {
            // Items are already linked in store()
            // We just need to mark them as 'dipinjam' in master data
            
            if (!$peminjaman->kode_peminjaman) {
                $peminjaman->kode_peminjaman = 'PMN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }

            $statusDipinjam = StatusPeminjaman::where('nama', 'dipinjam')->first();
            
            // Update all linked items
            foreach ($peminjaman->items as $pItem) {
                if ($pItem->sarprasItem) {
                    $pItem->sarprasItem->update(['status_peminjaman_id' => $statusDipinjam?->id]);
                }
            }

            $peminjaman->update([
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_penolakan' => null,
            ]);
            $peminjaman->syncStatus('disetujui');
        });

        return back()->with('success', 'Peminjaman berhasil disetujui ');
    }

    public function tolak(Request $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'menunggu') {
            return back()->withErrors(['status' => 'Peminjaman ini sudah diproses.']);
        }

        $request->validate([
            'alasan_penolakan' => ['nullable', 'string', 'max:500'],
        ]);
        
        // Note: Items were pre-reserved in `store`.
        // If rejected, we don't necessarily need to delete PeminjamanItems if we want to keep history of what was asked.
        // But since the status is 'ditolak', the scopeAvailable() in SarprasItem will automatically see these items as free again (because scope checks for status IN ['menunggu', 'disetujui']).
        // So no manual rollback of items needed unless we want to clean up DB.

        $peminjaman->update([
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);
        
        $peminjaman->syncStatus('ditolak');

        return back()->with('success', 'Peminjaman berhasil ditolak ');
    }

    public function kembalikan(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'disetujui') {
            return back()->withErrors(['status' => 'Hanya status disetujui yang bisa dikembalikan.']);
        }

        DB::transaction(function () use ($peminjaman) {
            // Mark all items as 'dikembalikan'
            $statusDikembalikan = StatusPeminjaman::where('nama', 'dikembalikan')->first();

            foreach ($peminjaman->items as $peminjamanItem) {
                 $peminjamanItem->sarprasItem->update(['status_peminjaman_id' => $statusDikembalikan?->id]);
            }

            $peminjaman->update([
                'tanggal_kembali_actual' => now(),
            ]);

            $peminjaman->syncStatus('dikembalikan');
        });

        return back()->with('success', 'Peminjaman berhasil dikembalikan ');
    }

    /**
     * Menampilkan struk peminjaman dengan QR code
     */
    public function struk(string $id)
    {
        $peminjaman = Peminjaman::with([
            'user.role',
            'items.sarprasItem.sarpras.kategori', 
            'items.sarprasItem.lokasi',
            'approver'
        ])->findOrFail($id);

        // 🔒 Proteksi: hanya admin, operator, atau user yang membuat peminjaman
        $isAuthorized = auth()->user()->id === $peminjaman->user_id ||
            in_array(auth()->user()?->role?->nama, ['admin', 'operator']);

        if (!$isAuthorized) {
            abort(403, 'Tidak punya akses melihat struk peminjaman');
        }

        // 🔒 hanya boleh lihat yang disetujui / dikembalikan
        if (!in_array($peminjaman->status, ['disetujui', 'dikembalikan'])) {
            return back()->withErrors([
                'status' => 'Peminjaman belum disetujui, tidak bisa melihat struk.'
            ]);
        }

        // Generate QR code
        $qrCode = QrCode::size(200)->generate($peminjaman->kode_peminjaman);

        return view('pages.peminjaman.struk', [
            'title' => 'Struk Peminjaman',
            'peminjaman' => $peminjaman,
            'qrCode' => $qrCode,
        ]);
    }
}

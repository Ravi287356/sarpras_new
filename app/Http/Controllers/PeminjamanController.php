<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Sarpras;
use App\Models\StatusPeminjaman;
use App\Models\PeminjamanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PeminjamanController extends Controller
{
    public function available()
    {
        $items = Sarpras::with(['kategori', 'items'])
            ->whereNull('deleted_at')
            ->orderBy('nama', 'asc')
            ->get()
            ->filter(function ($sarpras) {
                // Count available items using the new scope logic manually or via relation
                // Ideally we use the scope on the relation, but here we loaded all items.
                // Let's filter the loaded items collection to minimize N+1 or reloading.
                
                // However, scopeAvailable() uses complex where clauses that are best executed in SQL.
                // Re-querying might be safer for accuracy, but let's try to replicate the logic in PHP for the preloaded collection 
                // OR better: load count with closure.
                
                // Let's rely on a fresh count using the scope for accuracy
                $count = $sarpras->items()->available()->count();

                // Add count to object for view
                $sarpras->jumlah_stok = $count;

                return $count > 0;
            })
            ->values();

        // ✅ sesuaikan dengan folder kamu (resources/views/pages/peminjaman/available.blade.php)
        return view('pages.peminjaman.available', [
            'title' => 'Sarpras Bisa Dipinjam',
            'items' => $items,
        ]);
    }

    public function create(string $sarpras_id)
    {
        $sarpras = Sarpras::whereNull('deleted_at')->findOrFail($sarpras_id);

        // Calculate available items using scope
        $available_count = $sarpras->items()->available()->count();
        $sarpras->jumlah_stok = $available_count;

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
            'tujuan' => ['nullable', 'string'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
        ]);

        DB::transaction(function () use ($request) {
            $sarpras = Sarpras::whereNull('deleted_at')->lockForUpdate()->findOrFail($request->sarpras_id);

            // Get exact available items using the scope and lock them
            $available_items = $sarpras->items()->available()->lockForUpdate()->limit($request->jumlah)->get();

            if ($available_items->count() < $request->jumlah) {
                // Throw validation exception manually to rollback transaction
                throw \Illuminate\Validation\ValidationException::withMessages(['jumlah' => 'Stok tidak cukup atau sudah dipesan orang lain.']);
            }

            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                // 'sarpras_id' => $sarpras->id, // Removed from schema
                // 'jumlah' => (int) $request->jumlah, // Removed from schema
                'tujuan' => $request->tujuan,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
                'status' => 'menunggu',
            ]);

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
            ->whereIn('status', ['disetujui', 'dikembalikan']) // ✅ FILTER PENTING
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
                'status' => 'disetujui',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'alasan_penolakan' => null,
            ]);
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
            'status' => 'ditolak',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

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
                'status' => 'dikembalikan',
                 'tanggal_kembali_actual' => now(),
            ]);
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

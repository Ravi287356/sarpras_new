<?php

namespace App\Http\Controllers;

use App\Models\KondisiAlat;
use App\Models\Pengembalian;
use App\Models\SarprasItem;
use App\Models\StatusPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetHealthController extends Controller
{
    public function index()
    {
        // 1. Total Asset Health Report (General count of items with issues)
        // We can define this as items that are broken OR lost
        $brokenCount = SarprasItem::whereHas('kondisi', function ($q) {
            $q->where('nama', 'like', '%rusak%');
        })->count();

        $lostCount = SarprasItem::whereHas('statusPeminjaman', function ($q) {
            $q->where('nama', 'hilang');
        })->orWhereHas('kondisi', function ($q) {
             $q->where('nama', 'like', '%hilang%');
        })->count();
        
        // Items requiring maintenance (often same as broken, but let's separate if needed, for now use broken)
        $totalIssues = $brokenCount + $lostCount;

        // 2. Rusak Count
        $rusakCount = $brokenCount;

        // 3. Top 10 (Just needed for the card, maybe show the top 1 name or just "Top 10")
        // The card in the image just labels it "Asset Health - Top 10" and shows "1 2" (maybe Rank 1 and 2 count? or just an ID). 
        // We'll prepare the data for the view.
        
        // 4. Hilang Count
        $hilangCount = $lostCount;
        
        return view('pages.admin.asset-health.index', [
            'title' => 'Asset Health Report',
            'totalIssues' => $totalIssues,
            'rusakCount' => $rusakCount,
            'hilangCount' => $hilangCount,
        ]);
    }

    public function rusak()
    {
        $items = SarprasItem::with(['sarpras.kategori', 'lokasi', 'kondisi'])
            ->whereHas('kondisi', function ($q) {
                $q->where('nama', 'like', '%rusak%');
            })
            ->latest('updated_at')
            ->get();

        // Append last return info manually to avoid complex subqueries in builder for now
        foreach ($items as $item) {
             // Find the last return that caused this damage? 
             // Or just the last return in general.
             // We look for the latest Pengembalian linked to this item via Peminjaman
             $lastReturn = Pengembalian::whereHas('peminjaman.items', function($q) use ($item) {
                 $q->where('sarpras_item_id', $item->id);
             })->latest('created_at')->first();
             
             $item->last_return = $lastReturn;
        }

        return view('pages.admin.asset-health.rusak', [
            'title' => 'Daftar Aset Rusak',
            'items' => $items
        ]);
    }

    public function hilang()
    {
        // Logic: Status Peminjaman 'hilang' OR Kondisi 'hilang'
        $items = SarprasItem::with(['sarpras.kategori', 'lokasi', 'statusPeminjaman', 'peminjamanItems.peminjaman.user'])
            ->whereHas('statusPeminjaman', function ($q) {
                $q->where('nama', 'hilang');
            })
            ->orWhereHas('kondisi', function ($q) {
                $q->where('nama', 'like', '%hilang%');
            })
            ->get();

        // For each item, try to find the last borrower
        // We can get this from the latest peminjamanItem linked to this sarprasItem
        foreach ($items as $item) {
            $lastPeminjamanItem = $item->peminjamanItems()
                ->whereHas('peminjaman', function($q) {
                    $q->orderBy('tanggal_pinjam', 'desc');
                })
                ->first(); // relationships are simpler to traverse in view if eager loaded right, but determining *last* might need logic here or in view.
            
            // Allow view to access last borrower
            $item->last_peminjaman = $lastPeminjamanItem?->peminjaman;
        }

        return view('pages.admin.asset-health.hilang', [
            'title' => 'Daftar Aset Hilang',
            'items' => $items
        ]);
    }

    public function top10()
    {
        // Count how many times an item has been returned with condition 'Rusak'
        // We look at 'Pengembalian' -> 'kondisi_alat_id' (Rusak) -> 'peminjaman' -> 'items' -> 'sarpras_item_id'
        // This is tricky because Pengembalian is linked to Peminjaman, and Peminjaman has many items.
        // Usually 1 peminjaman = multiple items. 
        // If the *entire* peminjaman is returned as "Rusak", does it apply to all items?
        // In `PengembalianController.store`, we see:
        // $pengembalian = Pengembalian::create([... 'kondisi_alat_id' => $request->kondisi_alat_id ...]);
        // And then:
        // foreach ($peminjaman->items as $item) { $item->sarprasItem->update(['status_peminjaman_id' => ...]); }
        // It seems the 'condition' in Pengembalian applies to the whole batch or is a general summary. 
        // However, `SarprasItem` has its own `kondisi_alat_id`.
        // If we want to track which specific ITEM is frequently broken, we should rely on the *history* of that item being marked as 'Rusak'.
        
        // Since we don't have a specific "ItemHistory" table that logs every condition change, we have to rely on `Pengembalian` records linked to the items.
        // Problem: `Pengembalian` is 1-to-1 with `Peminjaman`. `Peminjaman` is 1-to-Many with `Items`.
        // If I borrow 5 laptops and return them, and the Pengembalian says "Rusak", it implies all 5 are broken? Or just one?
        // The current system seems to treat the Peminjaman return as a single event with a single condition.
        
        // Best Approximation: Count how many times a SarprasItem was part of a Peminjaman that was returned with 'Rusak' condition.
        
        $brokenConditionIds = KondisiAlat::where('nama', 'like', '%rusak%')->pluck('id');

        $topItems = SarprasItem::select('sarpras_items.*')
            ->selectSub(function ($query) use ($brokenConditionIds) {
                $query->selectRaw('count(*)')
                    ->from('peminjaman_items')
                    ->join('peminjaman', 'peminjaman_items.peminjaman_id', '=', 'peminjaman.id')
                    ->join('pengembalian', 'peminjaman.id', '=', 'pengembalian.peminjaman_id')
                    ->whereColumn('peminjaman_items.sarpras_item_id', 'sarpras_items.id')
                    ->whereIn('pengembalian.kondisi_alat_id', $brokenConditionIds);
            }, 'breakdown_count')
            ->having('breakdown_count', '>', 0)
            ->orderByDesc('breakdown_count')
            ->limit(10)
            ->with(['sarpras', 'lokasi']) // eager load
            ->get();

        return view('pages.admin.asset-health.top10', [
            'title' => 'Top 10 Aset Sering Rusak',
            'items' => $topItems
        ]);
    }
}

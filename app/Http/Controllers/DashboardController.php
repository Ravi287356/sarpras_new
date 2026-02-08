<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KategoriSarpras;
use App\Models\KondisiAlat;
use App\Models\Pengembalian;
use App\Models\PengembalianItem;
use App\Models\SarprasItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        // Existing dashboard data
        $totalUsers = User::count();
        $totalKategori = KategoriSarpras::count();

        // Asset Health Report Data
        $brokenConditionIds = KondisiAlat::where('nama', 'like', '%rusak%')->pluck('id');
        
        // Rusak items / Needs Maintenance
        $rusakItems = SarprasItem::with(['sarpras.kategori', 'lokasi', 'kondisi', 'statusPeminjaman'])
            ->where(function($query) {
                $query->whereHas('kondisi', function ($q) {
                    $q->where('nama', 'like', '%rusak%')
                      ->orWhere('nama', 'like', '%maintenance%');
                })
                ->orWhereHas('statusPeminjaman', function ($q) {
                    $q->where('nama', 'like', '%maintenance%');
                });
            })
            ->latest('updated_at')
            ->take(5) // Show top 5 in dashboard
            ->get();

        // Append last return info
        foreach ($rusakItems as $item) {
            $lastReturnItem = PengembalianItem::where('sarpras_item_id', $item->id)
                ->latest('created_at')
                ->first();
            $item->last_return_item = $lastReturnItem;
        }

        // Hilang items
        $hilangItems = SarprasItem::with(['sarpras.kategori', 'lokasi', 'statusPeminjaman', 'peminjamanItems.peminjaman.user'])
            ->whereHas('statusPeminjaman', function ($q) {
                $q->where('nama', 'hilang');
            })
            ->orWhereHas('kondisi', function ($q) {
                $q->where('nama', 'like', '%hilang%');
            })
            ->take(5)
            ->get();

        foreach ($hilangItems as $item) {
            $lastPeminjamanItem = $item->peminjamanItems()
                ->whereHas('peminjaman', function($q) {
                    $q->orderBy('tanggal_pinjam', 'desc');
                })
                ->first();
            $item->last_peminjaman = $lastPeminjamanItem?->peminjaman;
        }

        // Summary Counts for Overview
        $countBaik = SarprasItem::whereHas('kondisi', function ($q) {
            $q->where('nama', 'Baik');
        })->where(function($q) {
            $q->whereDoesntHave('statusPeminjaman', function($sq) {
                $sq->where('nama', 'like', '%maintenance%');
            })->orWhereNull('status_peminjaman_id');
        })->count();

        $countRusak = SarprasItem::where(function($query) {
            $query->whereHas('kondisi', function ($q) {
                $q->where('nama', 'like', '%rusak%')
                  ->orWhere('nama', 'like', '%maintenance%');
            })
            ->orWhereHas('statusPeminjaman', function ($q) {
                $q->where('nama', 'like', '%maintenance%');
            });
        })->count();

        $countHilang = SarprasItem::whereHas('statusPeminjaman', function ($q) {
            $q->where('nama', 'hilang');
        })->orWhereHas('kondisi', function ($q) {
            $q->where('nama', 'like', '%hilang%');
        })->count();

        // Top 5 frequent breakdowns (for overview)
        $topItems = SarprasItem::select('sarpras_items.*')
            ->selectSub(function ($query) use ($brokenConditionIds) {
                $query->selectRaw('count(*)')
                    ->from('pengembalian_items')
                    ->whereColumn('pengembalian_items.sarpras_item_id', 'sarpras_items.id')
                    ->whereIn('pengembalian_items.kondisi_alat_id', $brokenConditionIds);
            }, 'breakdown_count')
            ->having('breakdown_count', '>', 0)
            ->orderByDesc('breakdown_count')
            ->limit(5)
            ->with(['sarpras.kategori', 'lokasi'])
            ->get();

        return view('pages.admin.dashboard', [
            'title'         => 'Dashboard',
            'totalUsers'    => $totalUsers,
            'totalKategori' => $totalKategori,
            'rusakItems'    => $rusakItems,
            'hilangItems'   => $hilangItems,
            'topItems'      => $topItems,
            'countBaik'     => $countBaik,
            'countRusak'    => $countRusak,
            'countHilang'   => $countHilang,
        ]);
    }

    public function operatorDashboard()
    {
        return view('pages.operator.dashboard', [
            'title' => 'Dashboard',
        ]);
    }

    public function userDashboard()
    {
        return view('pages.user.dashboard', [
            'title' => 'Dashboard',
        ]);
    }
}

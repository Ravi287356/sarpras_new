<?php

namespace App\Http\Controllers;

use App\Models\SarprasItem;
use App\Models\StatusPeminjaman;
use App\Models\KondisiAlat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceAlatController extends Controller
{
    public function index()
    {
        // Get all items that are either available or already in maintenance
        // Or better: show all items so admin can put any item into maintenance
        $items = SarprasItem::with(['sarpras', 'lokasi', 'kondisi', 'statusPeminjaman'])
            ->get();

        return view('pages.admin.maintenance.index', [
            'title' => 'Manajemen Maintenance',
            'items' => $items,
        ]);
    }

    public function startMaintenance(string $id)
    {
        $item = SarprasItem::findOrFail($id);
        
        // Status Maintenance ID 10
        $statusMaintenance = StatusPeminjaman::where('nama', 'Maintenance')->first();

        $item->update([
            'status_peminjaman_id' => $statusMaintenance?->id ?? 10,
        ]);

        return back()->with('success', 'Barang ' . $item->kode . ' sedang dalam maintenance 🛠️');
    }

    public function finishMaintenance(string $id)
    {
        $item = SarprasItem::findOrFail($id);
        
        // Status Tersedia ID 4
        // Kondisi Baik ID 1
        $statusTersedia = StatusPeminjaman::where('nama', 'tersedia')->first();
        $kondisiBaik = KondisiAlat::where('nama', 'Baik')->first();

        $item->update([
            'status_peminjaman_id' => $statusTersedia?->id ?? 4,
            'kondisi_alat_id' => $kondisiBaik?->id ?? 1,
        ]);

        return back()->with('success', 'Maintenance selesai. Barang ' . $item->kode . ' kini Tersedia dan dalam Kondisi Baik ✨');
    }
}

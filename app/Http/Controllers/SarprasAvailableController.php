<?php

namespace App\Http\Controllers;

use App\Models\Sarpras;

class SarprasAvailableController extends Controller
{
    public function index()
    {
        $role = auth()->user()?->role?->nama;

        $query = Sarpras::with(['kategori', 'items.lokasi', 'items.kondisi', 'items.statusPeminjaman'])
            ->orderBy('nama', 'asc');

        $sarpras_list = $query->get();

        // Group by Sarpras + Condition
        $items = collect();
        
        foreach ($sarpras_list as $sarpras) {
            // Get available items for this sarpras, grouped by condition AND status
            $groupedItems = $sarpras->items()
                ->available()
                ->with(['kondisi', 'lokasi', 'statusPeminjaman'])
                ->get()
                ->groupBy(function($item) {
                    return $item->kondisi_alat_id . '-' . ($item->status_peminjaman_id ?? 'null');
                });

            foreach ($groupedItems as $key => $itemsByGroup) {
                $firstItem = $itemsByGroup->first();
                
                // Clone the sarpras object to store group-specific data
                $group = clone $sarpras;
                $group->jumlah_stok = $itemsByGroup->count();
                $group->kondisi_saat_ini = $firstItem->kondisi?->nama ?? '-';
                $group->lokasi_saat_ini = $firstItem->lokasi?->nama ?? '-';
                $group->sample_item = $firstItem;
                $group->group_status_id = $firstItem->status_peminjaman_id;
                $group->group_kondisi_id = $firstItem->kondisi_alat_id;
                
                $items->push($group);
            }
        }

        // Sort: BUTUH MAINTENANCE at the top, then by nama ascending
        $items = $items->sortBy(function ($item) {
            // Return array for multi-criteria sort: [primary, secondary]
            // 0 for BUTUH MAINTENANCE (sorts first), 1 for others (sorts after)
            $priority = $item->sample_item?->getDisplayStatus() === 'BUTUH MAINTENANCE' ? 0 : 1;
            return [$priority, $item->nama];
        });

        // For users, the filtering is already implicit because we only push if $groupedItems is not empty
        // But let's be explicit and re-filter if needed (though foreach naturally handles empty)

        return view('pages.sarpras.available', [
            'title' => 'Sarpras Tersedia',
            'items' => $items,
            'role'  => $role,
        ]);
    }
}

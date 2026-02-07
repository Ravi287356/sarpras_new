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

        // Map sarpras with available items count
        $items = $sarpras_list->map(function ($sarpras) use ($role) {
            // Count available items using scope
            // Note: Since items are preloaded, we could filter in PHP, 
            // but scopeAvailable uses complex queries that are hard to replicate on collection perfectly without loading more data.
            // Let's rely on the query for accuracy (N+1 query per sarpras is acceptable for small datasets, 
            // but for larger ones we should use withCount with scope if Laravel supported it easily on relation, 
            // or just manual count like below).
            
            $available_count = $sarpras->items()->available()->count();

            // For users, only show sarpras with available items
            if ($role === 'user' && $available_count === 0) {
                return null;
            }

            // Add available count to sarpras object
            $sarpras->jumlah_stok = $available_count;
            
            // Get kondisi from first available item (fetch one to display sample condition)
            $firstAvailable = $sarpras->items()->available()->first();
            $sarpras->kondisi_saat_ini = $firstAvailable?->kondisi?->nama ?? '-';

            return $sarpras;
        })->filter(function ($item) {
            return $item !== null;
        })->values();

        return view('pages.sarpras.available', [
            'title' => 'Sarpras Tersedia',
            'items' => $items,
            'role'  => $role,
        ]);
    }
}

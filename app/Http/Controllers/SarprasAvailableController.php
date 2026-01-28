<?php

namespace App\Http\Controllers;

use App\Models\Sarpras;

class SarprasAvailableController extends Controller
{
    public function index()
    {
        $role = auth()->user()?->role?->nama;

        $query = Sarpras::with(['kategori', 'lokasi'])
            ->orderBy('nama', 'asc');

        // user hanya lihat stok > 0
        if ($role === 'user') {
            $query->where('jumlah_stok', '>', 0);
        }

        $items = $query->get();

        return view('pages.sarpras.available', [
            'title' => 'Sarpras Tersedia',
            'items' => $items,
            'role'  => $role,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\KondisiAlat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\PengembalianItem;
use App\Models\SarprasItem;
use App\Models\StatusPeminjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{

    public function index()
    {
        $kondisiAlat = KondisiAlat::all();
        return view('pages.pengembalian.pengembalian', [
            'title' => 'Pengembalian Sarpras',
            'kondisiAlat' => $kondisiAlat,
            'isWeekend' => now()->isWeekend()
        ]);
    }

    public function searchPeminjaman(Request $request)
    {
        // 🛑 Cegah pencarian pada hari Sabtu dan Minggu
        if (now()->isWeekend()) {
            return response()->json(['error' => 'Pengembalian tidak dilayani pada hari Sabtu dan Minggu.'], 403);
        }

        $request->validate([
            'kode_peminjaman' => 'required|string'
        ]);

        $peminjaman = Peminjaman::where('kode_peminjaman', $request->kode_peminjaman)
            ->with(['user', 'items.sarprasItem.sarpras.kategori', 'items.sarprasItem.lokasi', 'pengembalian'])
            ->first();

        if (!$peminjaman) {
            return response()->json(['error' => 'Kode peminjaman tidak ditemukan'], 404);
        }

        // Check status (must be approved/active)
        if ($peminjaman->status !== 'disetujui') {
            return response()->json(['error' => 'Peminjaman ini tidak dalam status dipinjam (Status: ' . $peminjaman->status . ')'], 400);
        }

        if ($peminjaman->pengembalian) {
            return response()->json(['error' => 'Peminjaman ini sudah dikembalikan'], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $peminjaman->id,
                'kode_peminjaman' => $peminjaman->kode_peminjaman,
                'user' => $peminjaman->user->username ?? '-',
                'items' => $peminjaman->items->map(function ($item) {
                    return [
                        'id' => $item->sarprasItem->id,
                        'nama' => $item->sarprasItem->sarpras->nama ?? '-',
                        'kode' => $item->sarprasItem->kode,
                        'lokasi' => $item->sarprasItem->lokasi->nama ?? '-',
                        'kategori' => $item->sarprasItem->sarpras->kategori->nama ?? '-'
                    ];
                })
            ]
        ]);
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'items.sarprasItem.sarpras.kategori',
            'items.sarprasItem.lokasi',
            'pengembalian'
        ])->findOrFail($id);

        if ($peminjaman->status !== 'disetujui') {
            return redirect()->route('pengembalian.index')->withErrors('Peminjaman ini tidak dalam status dipinjam');
        }

        if ($peminjaman->pengembalian) {
            return redirect()->route('pengembalian.index')->withErrors('Peminjaman ini sudah dikembalikan');
        }

        $kondisiAlat = KondisiAlat::all();

        return view('pages.pengembalian.form', [
            'title' => 'Konfirmasi Pengembalian',
            'peminjaman' => $peminjaman,
            'kondisiAlat' => $kondisiAlat,
            'isWeekend' => now()->isWeekend()
        ]);
    }

    public function store(Request $request)
    {
        // 🛑 Cegah pengembalian pada hari Sabtu dan Minggu
        if (now()->isWeekend()) {
            return back()->with('error', 'Pengembalian tidak dilayani pada hari Sabtu dan Minggu.');
        }

        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'catatan_petugas' => 'required|string', // Already required in form, but just in case
            'items' => 'required|array',
            'items.*.sarpras_item_id' => 'required|exists:sarpras_items,id',
            'items.*.kondisi_alat_id' => 'required|exists:kondisi_alat,id',
            'items.*.deskripsi_kerusakan' => 'required_if:items.*.kondisi_alat_id,2,3,4|nullable|string',
            'items.*.foto' => 'required_if:items.*.kondisi_alat_id,2,3|nullable|image|mimes:jpg,jpeg,png|max:2048'
        ], [
            'items.*.deskripsi_kerusakan.required_if' => 'Deskripsi wajib diisi jika kondisi barang rusak atau hilang.',
            'items.*.foto.required_if' => 'Foto bukti wajib diupload jika kondisi barang rusak.',
        ]);

        $peminjaman = Peminjaman::with('items.sarprasItem')->findOrFail($request->peminjaman_id);

        if ($peminjaman->pengembalian) {
            return back()->withErrors('Peminjaman ini sudah dikembalikan');
        }

        DB::transaction(function () use ($request, $peminjaman) {
            // 1. Create main Pengembalian record
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'catatan_petugas' => $request->catatan_petugas,
                'approved_by' => Auth::id(),
                'tanggal_pengembalian' => now()
            ]);

            // 2. Update Peminjaman status
            $peminjaman->update([
                'tanggal_kembali_actual' => now(),
            ]);
            $peminjaman->syncStatus('dikembalikan');

            $statusTersedia = StatusPeminjaman::where('nama', 'tersedia')->first();
            $statusButuhMaintenance = StatusPeminjaman::where('nama', 'butuh maintenance')->first();

            // 3. Process each item
            foreach ($request->items as $itemId => $itemData) {
                $fotoPath = null;
                if ($request->hasFile("items.$itemId.foto")) {
                    $fotoPath = $request->file("items.$itemId.foto")
                        ->store('pengembalian', 'public');
                }

                // Create PengembalianItem
                PengembalianItem::create([
                    'pengembalian_id' => $pengembalian->id,
                    'sarpras_item_id' => $itemData['sarpras_item_id'],
                    'kondisi_alat_id' => $itemData['kondisi_alat_id'],
                    'deskripsi_kerusakan' => $itemData['deskripsi_kerusakan'] ?? null,
                    'foto_url' => $fotoPath,
                ]);

                // Update SarprasItem
                $sarprasItem = SarprasItem::findOrFail($itemData['sarpras_item_id']);
                $kondisiAlat = KondisiAlat::findOrFail($itemData['kondisi_alat_id']);

                $newStatusId = $statusTersedia?->id;
                if (in_array($kondisiAlat->nama, ['Rusak Ringan', 'Rusak Berat'])) {
                    $newStatusId = $statusButuhMaintenance?->id ?? $newStatusId;
                }

                $sarprasItem->update([
                    'kondisi_alat_id' => $itemData['kondisi_alat_id'],
                    'status_peminjaman_id' => $newStatusId
                ]);
            }
        });

        // ✅ Log activity
        $this->logActivity(
            aksi: 'PENGEMBALIAN_CATAT',
            deskripsi: 'Mencatat pengembalian untuk: ' . $peminjaman->kode_peminjaman
        );

        return redirect()->route('pengembalian.index')->with('success', 'Pengembalian berhasil dicatat');
    }

    public function riwayat()
    {
        return view('pages.pengembalian.riwayat', [
            'pengembalian' => Pengembalian::with(['approvedBy', 'peminjaman.user', 'items.sarprasItem.sarpras', 'items.kondisiAlat'])
                ->latest('tanggal_pengembalian')
                ->get(),
            'title' => 'Riwayat Pengembalian'
        ]);
    }

    public function detail($id)
    {
        $pengembalian = Pengembalian::with([
            'approvedBy',
            'peminjaman.user',
            'items.sarprasItem.sarpras',
            'items.sarprasItem.lokasi',
            'items.kondisiAlat'
        ])->findOrFail($id);

        return view('pages.pengembalian.detail', [
            'title' => 'Detail Pengembalian',
            'pengembalian' => $pengembalian
        ]);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Sarpras;
use App\Models\SarprasItem;
use App\Models\Lokasi;
use App\Models\KondisiAlat;
use App\Models\StatusPeminjaman;
use Illuminate\Http\Request;

class SarprasItemController extends Controller
{
    /**
     * Show form to create a new item
     */
    public function create(Sarpras $sarpras)
    {
        return view('pages.sarpras.item_create', [
            'title'     => 'Tambah Item: ' . $sarpras->nama,
            'sarpras'   => $sarpras,
            'lokasis'   => Lokasi::orderBy('nama', 'asc')->get(),
            'kondisis'  => KondisiAlat::orderBy('nama', 'asc')->get(),
            'statuses'  => StatusPeminjaman::orderBy('nama', 'asc')->get(),
        ]);
    }

    /**
     * Store a new item
     */
    public function store(Request $request, Sarpras $sarpras)
    {
        $request->validate([
            'kode'                      => 'required|string|unique:sarpras_items,kode',
            'lokasi_id'                 => 'required|exists:lokasi,id',
            'kondisi_alat_id'           => 'nullable|exists:kondisi_alat,id',
            'status_peminjaman_id'      => 'nullable|exists:status_peminjaman,id',
        ]);

        // Set default status to 'tersedia' if not provided
        $statusPeminjamanId = $request->status_peminjaman_id;
        if (!$statusPeminjamanId) {
            $tersedia = StatusPeminjaman::where('nama', 'tersedia')->first();
            if (!$tersedia) {
                return back()->withErrors(['error' => 'Status "tersedia" tidak ditemukan. Silakan jalankan seeder terlebih dahulu.']);
            }
            $statusPeminjamanId = $tersedia->id;
        }

        SarprasItem::create([
            'sarpras_id'                => $sarpras->id,
            'kode'                      => $request->kode,
            'lokasi_id'                 => $request->lokasi_id,
            'kondisi_alat_id'           => $request->kondisi_alat_id,
            'status_peminjaman_id'      => $statusPeminjamanId,
        ]);

        // ✅ Log activity
        $this->logActivity(
            aksi: 'ITEM_SARPRAS_BUAT',
            deskripsi: 'Buat item sarpras: ' . $request->kode . ' untuk ' . $sarpras->nama
        );

        return redirect()
            ->route('admin.sarpras.items', $sarpras->id)
            ->with('success', 'Item berhasil ditambahkan ✅');
    }

    /**
     * Show form to edit an item
     */
    public function edit(SarprasItem $sarprasItem)
    {
        $sarprasItem->load('sarpras');

        return view('pages.sarpras.item_edit', [
            'title'     => 'Edit Item: ' . $sarprasItem->kode,
            'item'      => $sarprasItem,
            'sarpras'   => $sarprasItem->sarpras,
            'lokasis'   => Lokasi::orderBy('nama', 'asc')->get(),
            'kondisis'  => KondisiAlat::orderBy('nama', 'asc')->get(),
            'statuses'  => StatusPeminjaman::orderBy('nama', 'asc')->get(),
        ]);
    }

    /**
     * Update an item
     */
    public function update(Request $request, SarprasItem $sarprasItem)
    {
        $request->validate([
            'kode'                      => 'required|string|unique:sarpras_items,kode,' . $sarprasItem->id,
            'lokasi_id'                 => 'required|exists:lokasi,id',
            'kondisi_alat_id'           => 'nullable|exists:kondisi_alat,id',
            'status_peminjaman_id'      => 'nullable|exists:status_peminjaman,id',
        ]);

        // Set default status to 'tersedia' if not provided
        $statusPeminjamanId = $request->status_peminjaman_id;
        if (!$statusPeminjamanId) {
            $tersedia = StatusPeminjaman::where('nama', 'tersedia')->first();
            if (!$tersedia) {
                return back()->withErrors(['error' => 'Status "tersedia" tidak ditemukan. Silakan jalankan seeder terlebih dahulu.']);
            }
            $statusPeminjamanId = $tersedia->id;
        }

        $sarprasItem->update([
            'kode'                      => $request->kode,
            'lokasi_id'                 => $request->lokasi_id,
            'kondisi_alat_id'           => $request->kondisi_alat_id,
            'status_peminjaman_id'      => $statusPeminjamanId,
        ]);

        // ✅ Log activity
        $this->logActivity(
            aksi: 'ITEM_SARPRAS_UPDATE',
            deskripsi: 'Update item sarpras: ' . $request->kode
        );

        return redirect()
            ->route('admin.sarpras.items', $sarprasItem->sarpras_id)
            ->with('success', 'Item berhasil diupdate ✅');
    }

    /**
     * Delete an item
     */
    public function destroy(SarprasItem $sarprasItem)
    {
        $sarprasId = $sarprasItem->sarpras_id;
        $kode = $sarprasItem->kode;

        // ✅ Log activity
        $this->logActivity(
            aksi: 'ITEM_SARPRAS_HAPUS',
            deskripsi: 'Hapus item sarpras: ' . $kode
        );

        $sarprasItem->delete();

        return redirect()
            ->route('admin.sarpras.items', $sarprasId)
            ->with('success', 'Item berhasil dihapus ✅');
    }

    public function trashed()
    {
        $items = SarprasItem::onlyTrashed()
            ->with(['sarpras', 'lokasi', 'kondisi'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('pages.admin.sarpras_item.restore', [
            'title' => 'Item Sarpras Terhapus',
            'items' => $items,
        ]);
    }

    public function restore($id)
    {
        $item = SarprasItem::withTrashed()->findOrFail($id);
        
        if (!$item->trashed()) {
            return back()->with('error', 'Item tidak berada di tempat sampah');
        }

        $kode = $item->kode;
        $item->restore();

        // ✅ Log activity
        $this->logActivity(
            aksi: 'ITEM_SARPRAS_RESTORE',
            deskripsi: 'Restore item sarpras: ' . $kode
        );

        return back()->with('success', 'Item berhasil dipulihkan ✅');
    }
}

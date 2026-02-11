<?php

namespace App\Http\Controllers;

use App\Models\KategoriSarpras;
use App\Models\Lokasi;
use App\Models\Sarpras;
use App\Models\SarprasItem;
use App\Models\KondisiAlat;
use App\Models\StatusPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SarprasController extends Controller
{
    public function index()
    {
        $items = Sarpras::with('kategori', 'items')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.sarpras.index', [
            'title' => 'Data Sarpras',
            'items' => $items,
        ]);
    }

    /**
     * Show sarpras items (inventory) for a single sarpras
     */
    public function items(Sarpras $sarpras)
    {
        $sarpras->load(['items.lokasi', 'items.kondisi', 'items.statusPeminjaman']);

        return view('pages.sarpras.items', [
            'title' => 'Detail Inventory: ' . $sarpras->nama,
            'sarpras' => $sarpras,
        ]);
    }

    public function create()
    {
        return view('pages.sarpras.create', [
            'title'     => 'Tambah Sarpras',
            'kategoris' => KategoriSarpras::orderBy('nama', 'asc')->get(),
            'lokasis'   => Lokasi::orderBy('nama', 'asc')->get(),
            'kondisis'  => KondisiAlat::orderBy('nama', 'asc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:255',
            'kategori_id'       => 'required|exists:kategori_sarpras,id',
            'lokasi_id'         => 'required|exists:lokasi,id',
            'kondisi_alat_id'   => 'nullable|exists:kondisi_alat,id',
            'jumlah'            => 'required|integer|min:1',
        ]);

        // Create sarpras
        $sarpras = Sarpras::create([
            'nama'        => $request->nama,
            'kategori_id' => $request->kategori_id,
        ]);

        // Determine status_peminjaman_id based on kondisi
        // Get kondisi alat yang dipilih untuk check apakah rusak atau baik
        $kondisiAlat = $request->kondisi_alat_id ? KondisiAlat::find($request->kondisi_alat_id) : null;

        // Determine status based on kondisi:
        // - Jika kondisi rusak → "butuh maintenance"
        // - Jika kondisi baik/normal atau tidak ada → "tersedia"
        $statusPeminjamanId = null;
        
        if ($kondisiAlat && stripos($kondisiAlat->nama, 'rusak') !== false) {
            // Kondisi rusak → set "butuh maintenance"
            $maintenance = StatusPeminjaman::where('nama', 'butuh maintenance')->first();
            $statusPeminjamanId = $maintenance?->id;
        }
        
        // Jika tidak ada status atau kondisi normal, set ke "tersedia"
        if (!$statusPeminjamanId) {
            $tersedia = StatusPeminjaman::where('nama', 'tersedia')->first();
            if (!$tersedia) {
                return back()->withErrors(['error' => 'Status "tersedia" tidak ditemukan. Silakan jalankan seeder terlebih dahulu.']);
            }
            $statusPeminjamanId = $tersedia->id;
        }

        // Auto-create sarpras_items sesuai jumlah dengan transaction
        // Pessimistic locking di generateKode() mencegah race condition
        DB::transaction(function () use ($sarpras, $request, $statusPeminjamanId) {
            for ($i = 1; $i <= $request->jumlah; $i++) {
                SarprasItem::create([
                    'sarpras_id'            => $sarpras->id,
                    'lokasi_id'             => $request->lokasi_id,
                    'kondisi_alat_id'       => $request->kondisi_alat_id,
                    'status_peminjaman_id'  => $statusPeminjamanId,
                ]);
            }
        });

        // ✅ Log activity
        $this->logActivity(
            aksi: 'SARPRAS_BUAT',
            deskripsi: 'Buat sarpras: ' . $request->nama . ' (jumlah item: ' . $request->jumlah . ')'
        );

        return redirect()->route('admin.sarpras.index')->with('success', 'Sarpras dan ' . $request->jumlah . ' item berhasil ditambahkan ✅');
    }

    public function edit(Sarpras $sarpras)
    {
        return view('pages.sarpras.edit', [
            'title'     => 'Edit Sarpras',
            'sarpras'   => $sarpras,
            'kategoris' => KategoriSarpras::orderBy('nama', 'asc')->get(),
        ]);
    }

    public function update(Request $request, Sarpras $sarpras)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_sarpras,id',
        ]);

        $sarpras->update([
            'nama'        => $request->nama,
            'kategori_id' => $request->kategori_id,
        ]);

        // ✅ Log activity
        $this->logActivity(
            aksi: 'SARPRAS_UPDATE',
            deskripsi: 'Update sarpras: ' . $request->nama
        );

        return redirect()
            ->route('admin.sarpras.index')
            ->with('success', 'Sarpras berhasil diupdate ✅');
    }


    public function destroy(Sarpras $sarpras)
    {
        // ✅ Log activity
        $this->logActivity(
            aksi: 'SARPRAS_HAPUS',
            deskripsi: 'Hapus sarpras: ' . $sarpras->nama
        );

        // ✅ ini akan mengisi deleted_at
        $sarpras->delete();

        return back()->with('success', 'Sarpras berhasil dihapus ✅');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\Sarpras;
use App\Models\SarprasItem;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $items = Lokasi::orderBy('nama')->get();

        return view('pages.admin.lokasi.index', [
            'title' => 'Lokasi',
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('pages.admin.lokasi.create', [
            'title' => 'Tambah Lokasi',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:lokasi,nama',
        ]);

        Lokasi::create([
            'nama' => $request->nama,
        ]);

        // ✅ Log activity
        $this->logActivity(
            aksi: 'LOKASI_BUAT',
            deskripsi: 'Buat lokasi: ' . $request->nama
        );

        return redirect()
            ->route('admin.lokasi.index')
            ->with('success', 'Lokasi berhasil ditambahkan ✅');
    }

    public function edit(Lokasi $lokasi)
    {
        return view('pages.admin.lokasi.edit', [
            'title'  => 'Edit Lokasi',
            'lokasi' => $lokasi,
        ]);
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:lokasi,nama,' . $lokasi->id,
        ]);

        $lokasi->update([
            'nama' => $request->nama,
        ]);

        // ✅ Log activity
        $this->logActivity(
            aksi: 'LOKASI_UPDATE',
            deskripsi: 'Update lokasi: ' . $request->nama
        );

        return redirect()
            ->route('admin.lokasi.index')
            ->with('success', 'Lokasi berhasil diupdate ✅');
    }

    public function destroy(Lokasi $lokasi)
    {
        // CEK DULU apakah lokasi dipakai sarpras item
        $itemsAktif = SarprasItem::where('lokasi_id', $lokasi->id)
            ->whereNull('deleted_at')
            ->count();

        if ($itemsAktif > 0) {
            return back()->with(
                'error',
                'Lokasi tidak bisa dihapus karena masih digunakan oleh ' . $itemsAktif . ' item sarpras.'
            );
        }

        // ✅ Log activity
        $this->logActivity(
            aksi: 'LOKASI_HAPUS',
            deskripsi: 'Hapus lokasi: ' . $lokasi->nama
        );

        $lokasi->delete();

        return back()->with('success', 'Lokasi berhasil dihapus ✅');
    }
}

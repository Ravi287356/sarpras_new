<?php

namespace App\Http\Controllers;

use App\Models\KategoriSarpras;
use App\Models\Sarpras;
use Illuminate\Http\Request;

class KategoriSarprasController extends Controller
{
    public function index()
    {
        $kategoris = KategoriSarpras::orderBy('nama')->get();

        return view('pages.admin.kategori_sarpras.index', [
            'title' => 'Kategori Sarpras',
            'kategoris' => $kategoris,
        ]);
    }

    public function create()
    {
        return view('pages.admin.kategori_sarpras.create', [
            'title' => 'Tambah Kategori Sarpras',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_sarpras,nama',
            'deskripsi' => 'nullable|string',
        ]);

        KategoriSarpras::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        // ✅ Log activity
        $this->logActivity(
            aksi: 'KATEGORI_SARPRAS_BUAT',
            deskripsi: 'Buat kategori sarpras: ' . $request->nama
        );

        return redirect()
            ->route('admin.kategori_sarpras.index')
            ->with('success', 'Kategori berhasil ditambahkan ✅');
    }

    public function edit(KategoriSarpras $kategori)
    {
        return view('pages.admin.kategori_sarpras.edit', [
            'title' => 'Edit Kategori Sarpras',
            'kategori' => $kategori,
        ]);
    }

    public function update(Request $request, KategoriSarpras $kategori)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_sarpras,nama,' . $kategori->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        // ✅ Log activity
        $this->logActivity(
            aksi: 'KATEGORI_SARPRAS_UPDATE',
            deskripsi: 'Update kategori sarpras: ' . $request->nama
        );

        return redirect()
            ->route('admin.kategori_sarpras.index')
            ->with('success', 'Kategori berhasil diupdate ✅');
    }

    public function destroy(KategoriSarpras $kategori)
    {
        // CEK DULU apakah kategori dipakai sarpras
        $sarprasAktif = Sarpras::where('kategori_id', $kategori->id)
            ->whereNull('deleted_at')
            ->count();

        if ($sarprasAktif > 0) {
            return back()->with(
                'error',
                'Kategori tidak bisa dihapus karena masih digunakan oleh ' . $sarprasAktif . ' sarpras.'
            );
        }

        // ✅ Log activity
        $this->logActivity(
            aksi: 'KATEGORI_SARPRAS_HAPUS',
            deskripsi: 'Hapus kategori sarpras: ' . $kategori->nama
        );

        $kategori->delete();

        return back()->with('success', 'Kategori berhasil dihapus ✅');
    }
}

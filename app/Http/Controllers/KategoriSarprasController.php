<?php

namespace App\Http\Controllers;

use App\Models\KategoriSarpras;
use Illuminate\Http\Request;

class KategoriSarprasController extends Controller
{
    public function index()
    {
        $kategoris = KategoriSarpras::orderBy('id', 'asc')->get();

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

        return redirect()->route('admin.kategori_sarpras.index')->with('success', 'Kategori berhasil ditambahkan ✅');
    }

    public function edit($id)
    {
        $kategori = KategoriSarpras::findOrFail($id);

        return view('pages.admin.kategori_sarpras.edit', [
            'title' => 'Edit Kategori Sarpras',
            'kategori' => $kategori,
        ]);
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriSarpras::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_sarpras,nama,' . $kategori->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.kategori_sarpras.index')->with('success', 'Kategori berhasil diupdate ✅');
    }

   public function destroy($id)
{
    $kategori = KategoriSarpras::findOrFail($id);
    $kategori->delete(); // <- ini yang benar untuk soft delete

    return back()->with('success', 'Kategori berhasil dihapus ');
}

}

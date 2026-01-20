<?php

namespace App\Http\Controllers;

use App\Models\KategoriSarpras;
use Illuminate\Http\Request;

class SarprasController extends Controller
{
    // LIST
    public function index()
    {
        $kategoris = KategoriSarpras::orderBy('id', 'asc')->get(); // lama di atas

        return view('pages.admin.kategori_sarpras.index', [
            'title' => 'Kategori Sarpras',
            'kategoris' => $kategoris
        ]);
    }

    // FORM CREATE
    public function create()
    {
        return view('pages.admin.kategori_sarpras.create', [
            'title' => 'Tambah Kategori Sarpras',
        ]);
    }

    // SIMPAN
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

        return redirect()->route('admin.kategori_sarpras.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // FORM EDIT
    public function edit($id)
    {
        $kategori = KategoriSarpras::findOrFail($id);

        return view('pages.admin.kategori_sarpras.edit', [
            'title' => 'Edit Kategori Sarpras',
            'kategori' => $kategori
        ]);
    }

    // UPDATE
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

        return redirect()->route('admin.kategori_sarpras.index')->with('success', 'Kategori berhasil diupdate.');
    }

    // HAPUS (soft delete)
    public function destroy($id)
    {
        $kategori = KategoriSarpras::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.kategori_sarpras.index')->with('success', 'Kategori berhasil dihapus.');
    }
}

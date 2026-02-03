<?php

namespace App\Http\Controllers;

use App\Models\KategoriSarpras;
use App\Models\Lokasi;
use App\Models\Sarpras;
use App\Helpers\CodeGenerator;
use Illuminate\Http\Request;

class SarprasController extends Controller
{
    public function index()
    {
        $items = Sarpras::with(['kategori', 'lokasi'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.sarpras.index', [
            'title' => 'Data Sarpras',
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('pages.sarpras.create', [
            'title'     => 'Tambah Sarpras',
            'kategoris' => KategoriSarpras::orderBy('nama', 'asc')->get(),
            'lokasis'   => Lokasi::orderBy('nama', 'asc')->get(),
        ]);
    }

    /**
     * Generate preview kode untuk form
     */
    public function generateCode(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_sarpras,id',
            'lokasi_id'   => 'required|exists:lokasi,id',
            'nama'        => 'required|string|max:255',
        ]);

        try {
            $code = CodeGenerator::generate(
                $request->kategori_id,
                $request->lokasi_id,
                $request->nama
            );

            return response()->json([
                'success' => true,
                'code'    => $code,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'             => 'required|string|max:255',
            'kategori_id'      => 'required|exists:kategori_sarpras,id',
            'lokasi_id'        => 'required|exists:lokasi,id',
            'jumlah_stok'      => 'required|integer|min:0',
            'kondisi_saat_ini' => 'nullable|string|max:255',
        ]);

        // Kode akan di-generate otomatis di Model Sarpras boot method
        Sarpras::create([
            'nama'             => $request->nama,
            'kategori_id'      => $request->kategori_id,
            'lokasi_id'        => $request->lokasi_id,
            'jumlah_stok'      => $request->jumlah_stok,
            'kondisi_saat_ini' => $request->kondisi_saat_ini,
        ]);

        return redirect()->route('admin.sarpras.index')->with('success', 'Sarpras berhasil ditambahkan ✅');
    }

    public function edit(Sarpras $sarpras)
    {
        return view('pages.sarpras.edit', [
            'title'     => 'Edit Sarpras',
            'sarpras'   => $sarpras,
            'kategoris' => KategoriSarpras::orderBy('nama', 'asc')->get(),
            'lokasis'   => Lokasi::orderBy('nama', 'asc')->get(),
        ]);
    }

    public function update(Request $request, Sarpras $sarpras)
    {
        $request->validate([
            'kode'             => 'required|string|max:255|unique:sarpras,kode,' . $sarpras->id,
            'nama'             => 'required|string|max:255',
            'kategori_id'      => 'required|exists:kategori_sarpras,id',
            'lokasi_id'        => 'required|exists:lokasi,id',
            'jumlah_stok'      => 'required|integer|min:0',
            'kondisi_saat_ini' => 'nullable|string|max:255',
        ]);

        $sarpras->update([
            'kode'             => $request->kode,
            'nama'             => $request->nama,
            'kategori_id'      => $request->kategori_id,
            'lokasi_id'        => $request->lokasi_id,
            'jumlah_stok'      => $request->jumlah_stok,
            'kondisi_saat_ini' => $request->kondisi_saat_ini,
        ]);

        return redirect()->route('admin.sarpras.index')->with('success', 'Sarpras berhasil diupdate ✅');
    }

    public function destroy(Sarpras $sarpras)
    {
        // ✅ ini akan mengisi deleted_at
        $sarpras->delete();

        return back()->with('success', 'Sarpras berhasil dihapus ✅');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    /**
     * USER - form pengaduan
     */
    public function create()
    {
        return view('pages.pengaduan.create', [
            'title' => 'Ajukan Pengaduan'
        ]);
    }

    /**
     * USER - simpan pengaduan
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi'    => 'required|string|max:255',
        ]);

        Pengaduan::create([
            'user_id'   => auth()->id(),
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
            'lokasi'    => $request->lokasi,
            'status'    => 'Belum Ditindaklanjuti',
        ]);

        // ✅ Log activity
        $this->logActivity(
            aksi: 'PENGADUAN_BUAT',
            deskripsi: 'Buat pengaduan: ' . $request->judul . ' (' . $request->lokasi . ')'
        );

        return redirect()
            ->route('user.pengaduan.riwayat')
            ->with('success', 'Pengaduan berhasil dikirim ✅');
    }

    /**
     * USER - riwayat pengaduan sendiri
     */
    public function riwayatUser()
    {
        $items = Pengaduan::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pages.pengaduan.riwayat_user', [
            'title' => 'Riwayat Pengaduan',
            'items' => $items,
        ]);
    }

    /**
     * ADMIN / OPERATOR - semua pengaduan
     */
    public function index()
    {
        $items = Pengaduan::with('user')
            ->latest()
            ->paginate(15);

        return view('pages.pengaduan.index', [
            'title' => 'Data Pengaduan',
            'items' => $items,
        ]);
    }

    /**
     * ADMIN / OPERATOR - update status + catatan
     */
    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'status'  => 'required|in:Belum Ditindaklanjuti,Sedang Diproses,Selesai,Ditutup',
            'catatan' => 'nullable|string',
        ]);

        if ($request->filled('catatan')) {
            $pengaduan->deskripsi .=
                "\n\n---\nCatatan Petugas (" .
                auth()->user()->name . " | " .
                now()->format('d-m-Y H:i') .
                "):\n" . $request->catatan;
        }

        $pengaduan->status = $request->status;
        $pengaduan->save();

        // ✅ Log activity
        $this->logActivity(
            aksi: 'PENGADUAN_UPDATE_STATUS',
            deskripsi: 'Update status pengaduan "' . $pengaduan->judul . '" menjadi ' . $request->status . ' (' . $pengaduan->lokasi . ')'
        );

        return back()->with('success', 'Pengaduan berhasil diperbarui ✅');
    }
}

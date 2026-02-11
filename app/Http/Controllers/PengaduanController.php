<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use App\Models\Lokasi;
use App\Models\KategoriSarpras;
use App\Models\CatatanPengaduan;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    /**
     * USER - form pengaduan
     */
    public function create()
    {
        return view('pages.pengaduan.create', [
            'title' => 'Ajukan Pengaduan',
            'lokasis'  => Lokasi::all(),
            'kategoris' => KategoriSarpras::all(),
        ]);
    }

    /**
     * USER - simpan pengaduan
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'lokasi_id'   => 'nullable|string|exists:lokasi,id',
            'kategori_id' => 'nullable|string|exists:kategori_sarpras,id',
        ]);

        Pengaduan::create([
            'user_id'    => Auth::id(),
            'judul'      => $request->judul,
            'deskripsi'  => $request->deskripsi,
            'lokasi_id'  => $request->lokasi_id ?? null,
            'kategori_id'=> $request->kategori_id ?? null,
            'status'     => 'Belum Ditindaklanjuti',
        ]);

        $this->logActivity(
            aksi: 'PENGADUAN_BUAT',
            deskripsi: 'Buat pengaduan: ' . $request->judul
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
        $pengaduan = Pengaduan::where('user_id', auth()->id())
            ->with(['lokasi', 'kategori', 'catatanPengaduan.user'])
            ->latest()
            ->paginate(10);

        return view('pages.pengaduan.riwayat_user', [
            'title'     => 'Riwayat Pengaduan',
            'pengaduan' => $pengaduan,
        ]);
    }

    /**
     * ADMIN / OPERATOR - semua pengaduan
     */
    public function index()
    {
        $query = Pengaduan::with(['user', 'lokasi', 'kategori', 'catatanPengaduan.user']);

        // Filter by lokasi_id
        if (request('lokasi_id')) {
            $query->where('lokasi_id', request('lokasi_id'));
        }

        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        $pengaduan = $query->latest()->paginate(15);

        // get distinct lokasi from Lokasi table
        $lokasiList = Lokasi::select('id', 'nama')->get();

        return view('pages.pengaduan.index', [
            'title'     => 'Data Pengaduan',
            'pengaduan' => $pengaduan,
            'lokasi'    => $lokasiList,
        ]);
    }

    /**
     * ADMIN / OPERATOR - update status + catatan
     */
    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        // Restriction: If status is Selesai or Ditutup, cannot update anymore
        if (in_array($pengaduan->status, ['Selesai', 'Ditutup'])) {
            return redirect()->back()->with('error', 'Pengaduan ini sudah diselesaikan/ditutup dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'status'  => 'required|in:Belum Ditindaklanjuti,Sedang Diproses,Selesai,Ditutup',
            'catatan' => 'required|string',
        ]);

        // Update status
        $pengaduan->status = $request->status;
        $pengaduan->save();

        // If catatan provided, save to CatatanPengaduan table
        if ($request->filled('catatan')) {
            CatatanPengaduan::create([
                'pengaduan_id' => $pengaduan->id,
                'user_id'      => auth()->id(),
                'catatan'      => $request->catatan,
            ]);
        }

        $this->logActivity(
            aksi: 'PENGADUAN_UPDATE_STATUS',
            deskripsi: 'Update status pengaduan "' . $pengaduan->judul .
                '" menjadi ' . $request->status
        );

        return redirect()
            ->route(auth()->user()->role->nama . '.pengaduan.index')
            ->with('success', 'Status pengaduan berhasil diperbarui ✅');
    }

    /**
     * ADMIN / OPERATOR - lihat detail pengaduan
     */
    public function show(Pengaduan $pengaduan)
    {
        $pengaduan->load(['user', 'lokasi', 'kategori', 'catatanPengaduan.user']);

        return view('pages.pengaduan', [
            'title'     => 'Detail Pengaduan',
            'pengaduan' => $pengaduan,
        ]);
    }

    /**
     * ADMIN / OPERATOR - halaman form respond/tanggapi pengaduan
     */
    public function respond(Pengaduan $pengaduan)
    {
        $pengaduan->load(['user', 'lokasi', 'kategori', 'catatanPengaduan.user']);

        return view('pages.pengaduan.respond', [
            'title'     => 'Tanggapi Pengaduan',
            'pengaduan' => $pengaduan,
        ]);
    }

    /**
     * ADMIN / OPERATOR - export data pengaduan ke CSV
     */
    public function exportCSV()
    {
        $filename = 'pengaduan-' . date('Y-m-d-H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = array('ID', 'User', 'Judul', 'Lokasi', 'Kategori', 'Status', 'Tanggal', 'Deskripsi');

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query = Pengaduan::with(['user', 'lokasi', 'kategori'])->latest();

            // Filter logic same as index if needed, but usually export checks all or filtered via query params
            // For now, let's just export all or maybe reuse filter logic if requested.
            // Simplified: export ALL for now as per minimal implementation.
            
            // If user filtered in index, they might expect filtered export, but 
            // the route is separate and doesn't pass params by default in the view link.
            // The view link in index.blade.php is just route('...export').
            
            foreach ($query->cursor() as $item) {
                $row['ID']        = $item->id;
                $row['User']      = $item->user->username ?? '-';
                $row['Judul']     = $item->judul;
                $row['Lokasi']    = $item->lokasi->nama ?? '-';
                $row['Kategori']  = $item->kategori->nama ?? '-';
                $row['Status']    = $item->status;
                $row['Tanggal']   = $item->created_at->format('Y-m-d H:i:s');
                $row['Deskripsi'] = $item->deskripsi;

                fputcsv($file, array($row['ID'], $row['User'], $row['Judul'], $row['Lokasi'], $row['Kategori'], $row['Status'], $row['Tanggal'], $row['Deskripsi']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

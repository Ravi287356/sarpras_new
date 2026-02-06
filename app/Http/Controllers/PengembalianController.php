<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\KondisiAlat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\SarprasItem;
use App\Models\StatusPinjam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function index()
    {
        $kondisiAlat = KondisiAlat::all();
        return view('pages.public.pengembalian.index', [
            'title' => 'Pengembalian Sarpras',
            'kondisiAlat' => $kondisiAlat
        ]);
    }

    public function searchPeminjaman(Request $request)
    {
        $request->validate([
            'kode_peminjaman' => 'required|string'
        ]);

        $peminjaman = Peminjaman::where('kode_peminjaman', $request->kode_peminjaman)
            ->with(['user', 'peminjamanItem.sarprasItem.sarpras.kategori', 'peminjamanItem.sarprasItem.lokasi', 'statusPinjam', 'pengembalian'])
            ->first();

        if (!$peminjaman) {
            return response()->json(['error' => 'Kode peminjaman tidak ditemukan'], 404);
        }

        if ($peminjaman->statusPinjam->nama !== 'Dipinjam') {
            return response()->json(['error' => 'Peminjaman ini tidak dalam status dipinjam'], 400);
        }

        if ($peminjaman->pengembalian) {
            return response()->json(['error' => 'Peminjaman ini sudah dikembalikan'], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $peminjaman->id,
                'kode_peminjaman' => $peminjaman->kode_peminjaman,
                'user' => $peminjaman->user->username,
                'items' => $peminjaman->peminjamanItem->map(function ($item) {
                    return [
                        'id' => $item->sarprasItem->id,
                        'nama' => optional($item->sarprasItem->sarpras)->nama ?? '-',
                        'kode' => $item->sarprasItem->kode,
                        'lokasi' => optional($item->sarprasItem->lokasi)->nama ?? '-',
                        'kategori' => optional(optional($item->sarprasItem->sarpras)->kategori)->nama ?? '-'
                    ];
                })
            ]
        ]);
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'peminjamanItem.sarprasItem.sarpras.kategori',
            'peminjamanItem.sarprasItem.lokasi',
            'statusPinjam',
            'pengembalian'
        ])->findOrFail($id);

        if ($peminjaman->statusPinjam->nama !== 'Dipinjam') {
            return redirect()->route('pengembalian.index')->withErrors('Peminjaman ini tidak dalam status dipinjam');
        }

        if ($peminjaman->pengembalian) {
            return redirect()->route('pengembalian.index')->withErrors('Peminjaman ini sudah dikembalikan');
        }

        $kondisiAlat = KondisiAlat::all();

        return view('pages.public.pengembalian.form', [
            'title' => 'Konfirmasi Pengembalian',
            'peminjaman' => $peminjaman,
            'kondisiAlat' => $kondisiAlat
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'kondisi_alat_id' => 'required|exists:kondisi_alat,id',
            'deskripsi_kerusakan' => 'nullable|string',
            'catatan_petugas' => 'nullable|string',
            'foto_url' => 'nullable|images|mimes:jpg,jpeg,png|max:2048'
        ]);

        $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);

        if ($peminjaman->pengembalian) {
            return back()->withErrors('Peminjaman ini sudah dikembalikan');
        }

        $kondisiAlat = KondisiAlat::findOrFail($request->kondisi_alat_id);

        DB::transaction(function () use ($request, $peminjaman, $kondisiAlat) {
            $fotoPath = null;

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')
                    ->store('pengembalian', 'public');
            }


            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'kondisi_alat_id' => $request->kondisi_alat_id,
                'deskripsi_kerusakan' => $request->deskripsi_kerusakan,
                'catatan_petugas' => $request->catatan_petugas,
                'foto_url' => $fotoPath,
                'approved_by' => Auth::id(),
                'tanggal_pengembalian' => now()
            ]);

            Peminjaman::where('id', $peminjaman->id)->update([
                'tanggal_kembali' => now()
            ]);

            $statusTersedia = StatusPinjam::where('nama', 'Tersedia')->first();
            $statusButuhMaintenance = StatusPinjam::where('nama', 'Butuh Maintenance')->first();

            $newStatus = $statusTersedia;

            if (in_array($kondisiAlat->nama, ['Rusak Ringan', 'Rusak Berat'])) {
                if ($statusButuhMaintenance) {
                    $newStatus = $statusButuhMaintenance;
                }
            }

            foreach ($peminjaman->peminjamanItem as $item) {
                if ($item->sarprasItem) {
                    $item->sarprasItem->update(['status_pinjam_id' => $newStatus->id]);
                }
            }

            $statusDikembalikan = StatusPinjam::where('nama', 'Dikembalikan')->first();
            if ($statusDikembalikan) {
                $peminjaman->update(['status_pinjam_id' => $statusDikembalikan->id]);
            }
        });

        ActivityLogger::log(
            'Pengembalian Sarpras',
            'Menerima pengembalian peminjaman dengan KODE ' . $peminjaman->kode_peminjaman . ' - Kondisi: ' . $kondisiAlat->nama
        );

        return back()->with('success', 'Pengembalian berhasil dicatat');
    }

    public function riwayat()
    {
        return view('pages.public.pengembalian.riwayat', [
            'pengembalian' => Pengembalian::with('approvedBy', 'peminjaman', 'kondisiAlat')
                ->latest('tanggal_pengembalian')
                ->get(),
            'title' => 'Riwayat Pengembalian'
        ]);
    }

}

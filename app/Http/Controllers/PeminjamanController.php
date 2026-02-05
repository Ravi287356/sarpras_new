<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Sarpras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PeminjamanController extends Controller
{
    public function available()
    {
        $items = Sarpras::query()
            ->whereNull('deleted_at')
            ->where('jumlah_stok', '>', 0)
            ->orderBy('nama', 'asc')
            ->get();

        // ✅ sesuaikan dengan folder kamu (resources/views/pages/peminjaman/available.blade.php)
        return view('pages.peminjaman.available', [
            'title' => 'Sarpras Bisa Dipinjam',
            'items' => $items,
        ]);
    }

    public function create(string $sarpras_id)
    {
        $sarpras = Sarpras::whereNull('deleted_at')->findOrFail($sarpras_id);

        // ✅ sesuaikan (resources/views/pages/peminjaman/create.blade.php)
        return view('pages.peminjaman.create', [
            'title' => 'Ajukan Peminjaman',
            'sarpras' => $sarpras,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sarpras_id' => ['required', 'exists:sarpras,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tujuan' => ['nullable', 'string'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
        ]);

        $sarpras = Sarpras::whereNull('deleted_at')->findOrFail($request->sarpras_id);

        if ((int) $request->jumlah > (int) $sarpras->jumlah_stok) {
            return back()->withErrors(['jumlah' => 'Stok tidak cukup.'])->withInput();
        }

        Peminjaman::create([
            'user_id' => auth()->id(),
            'sarpras_id' => $sarpras->id,
            'jumlah' => (int) $request->jumlah,
            'tujuan' => $request->tujuan,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => 'menunggu',
        ]);

        return redirect()->route('user.peminjaman.riwayat')->with('success', 'Peminjaman berhasil diajukan ✅');
    }

    public function riwayatUser()
    {
        // ❌ ERROR sebelumnya biasanya: pakai ->get() lalu di blade dipanggil ->links()
        // ✅ FIX: pakai paginate() + kirim variabel "logs" sesuai blade kamu
        $logs = Peminjaman::query()
            ->with(['sarpras.kategori', 'sarpras.lokasi'])
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(10);

        // ✅ sesuai file kamu: resources/views/pages/peminjaman/riwayat.blade.php
        return view('pages.peminjaman.riwayat', [
            'title' => 'Riwayat Peminjaman',
            'logs' => $logs,
        ]);
    }

    public function riwayat()
    {
        $logs = Peminjaman::query()
            ->with([
                'user.role',
                'sarpras.kategori',
                'sarpras.lokasi',
                'approver'
            ])
            ->whereIn('status', ['disetujui', 'dikembalikan']) // ✅ FILTER PENTING
            ->latest('approved_at')
            ->paginate(10);

        return view('pages.peminjaman.riwayat_semua', [
            'title' => 'Riwayat Peminjaman (Semua User)',
            'logs' => $logs,
        ]);
    }


    public function indexPermintaan()
    {
        $items = Peminjaman::query()
            ->with(['user.role', 'sarpras.kategori', 'sarpras.lokasi'])
            ->where('status', 'menunggu')
            ->latest('created_at')
            ->paginate(20);

        // ✅ 1 view untuk admin & operator (sesuai struktur folder kamu)
        return view('pages.peminjaman.permintaan', [
            'title' => 'Permintaan Peminjaman',
            'items' => $items,
        ]);
    }

    public function indexAktif()
    {
        $items = Peminjaman::query()
            ->with(['user.role', 'sarpras.kategori', 'sarpras.lokasi'])
            ->where('status', 'disetujui')
            ->latest('approved_at')
            ->get();

        $role = auth()->user()?->role?->nama;

        $view = $role === 'admin'
            ? 'pages.peminjaman.aktif'
            : 'pages.peminjaman.aktif';

        return view($view, [
            'title' => 'Peminjaman Aktif',
            'items' => $items,
        ]);
    }

    public function setujui(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'menunggu') {
            return back()->withErrors(['status' => 'Peminjaman ini sudah diproses.']);
        }

        DB::transaction(function () use ($peminjaman) {
            $sarpras = Sarpras::lockForUpdate()->findOrFail($peminjaman->sarpras_id);

            if ((int) $peminjaman->jumlah > (int) $sarpras->jumlah_stok) {
                abort(422, 'Stok tidak cukup untuk menyetujui peminjaman.');
            }
            if (!$peminjaman->kode_peminjaman) {
                $peminjaman->kode_peminjaman = 'PMN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }

            $sarpras->update([
                'jumlah_stok' => (int) $sarpras->jumlah_stok - (int) $peminjaman->jumlah,
            ]);

            $peminjaman->update([
                'status' => 'disetujui',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'alasan_penolakan' => null,
            ]);
        });

        return back()->with('success', 'Peminjaman berhasil disetujui ');
    }

    public function tolak(Request $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'menunggu') {
            return back()->withErrors(['status' => 'Peminjaman ini sudah diproses.']);
        }

        $request->validate([
            'alasan_penolakan' => ['nullable', 'string', 'max:500'],
        ]);

        $peminjaman->update([
            'status' => 'ditolak',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        return back()->with('success', 'Peminjaman berhasil ditolak ');
    }

    public function kembalikan(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'disetujui') {
            return back()->withErrors(['status' => 'Hanya status disetujui yang bisa dikembalikan.']);
        }

        DB::transaction(function () use ($peminjaman) {
            $sarpras = Sarpras::lockForUpdate()->findOrFail($peminjaman->sarpras_id);

            $sarpras->update([
                'jumlah_stok' => (int) $sarpras->jumlah_stok + (int) $peminjaman->jumlah,
            ]);

            $peminjaman->update([
                'status' => 'dikembalikan',
            ]);
        });

        return back()->with('success', 'Peminjaman berhasil dikembalikan ');
    }

    /**
     * Menampilkan struk peminjaman dengan QR code
     */
    public function struk(string $id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'user.role',
            'sarpras.kategori',
            'sarpras.lokasi',
            'approver'
        ])->findOrFail($id);

        // 🔒 Proteksi: hanya admin, operator, atau user yang membuat peminjaman
        $isAuthorized = auth()->user()->id === $peminjaman->user_id ||
            in_array(auth()->user()?->role?->nama, ['admin', 'operator']);

        if (!$isAuthorized) {
            abort(403, 'Tidak punya akses melihat struk peminjaman');
        }

        // 🔒 hanya boleh lihat yang disetujui / dikembalikan
        if (!in_array($peminjaman->status, ['disetujui', 'dikembalikan'])) {
            return back()->withErrors([
                'status' => 'Peminjaman belum disetujui, tidak bisa melihat struk.'
            ]);
        }

        // Generate QR code
        $qrCode = QrCode::size(200)->generate($peminjaman->kode_peminjaman);

        return view('pages.peminjaman.struk', [
            'title' => 'Struk Peminjaman',
            'peminjaman' => $peminjaman,
            'qrCode' => $qrCode,
        ]);
    }
}

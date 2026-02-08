<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type', 'peminjaman');

        $data = collect();

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            if ($type === 'peminjaman') {
                $data = Peminjaman::with(['user', 'items.sarprasItem.sarpras', 'approver'])
                    ->whereBetween('tanggal_pinjam', [$start, $end])
                    ->orderBy('tanggal_pinjam', 'desc')
                    ->get();
            } else {
                $data = Pengaduan::with(['user', 'lokasi', 'kategori'])
                    ->whereBetween('created_at', [$start, $end])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return view('pages.admin.laporan.index', [
            'title' => 'Laporan Sarpras',
            'type' => $type,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'data' => $data,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type', 'peminjaman');

        if (!$startDate || !$endDate) {
            return back()->with('error', 'Pilih rentang tanggal terlebih dahulu.');
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $filename = "Laporan_" . ucfirst($type) . "_" . $start->format('Ymd') . "-" . $end->format('Ymd') . ".xls";

        if ($type === 'peminjaman') {
            $data = Peminjaman::with(['user', 'items.sarprasItem.sarpras', 'approver'])
                ->whereBetween('tanggal_pinjam', [$start, $end])
                ->orderBy('tanggal_pinjam', 'desc')
                ->get();

            $view = view('pages.admin.laporan.peminjaman_excel', compact('data', 'startDate', 'endDate'));
        } else {
            $data = Pengaduan::with(['user', 'lokasi', 'kategori'])
                ->whereBetween('created_at', [$start, $end])
                ->orderBy('created_at', 'desc')
                ->get();

            $view = view('pages.admin.laporan.pengaduan_excel', compact('data', 'startDate', 'endDate'));
        }

        return response($view)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}

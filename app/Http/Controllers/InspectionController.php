<?php

namespace App\Http\Controllers;

use App\Models\Sarpras;
use App\Models\SarprasItem;
use App\Models\Inspection;
use App\Models\InspectionChecklist;
use App\Models\InspectionResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InspectionController extends Controller
{
    /**
     * Display a listing of Sarpras types to manage their checklists.
     */
    public function indexChecklists()
    {
        $sarpras = Sarpras::with('kategori')->get();
        return view('pages.admin.inspeksi.checklists', compact('sarpras'));
    }

    /**
     * Show checklists for a specific Sarpras type.
     */
    public function showChecklist(Sarpras $sarpras)
    {
        $checklists = InspectionChecklist::where('sarpras_id', $sarpras->id)->get();
        return view('pages.admin.inspeksi.checklist_show', compact('sarpras', 'checklists'));
    }

    /**
     * Store a new checklist item.
     */
    public function storeChecklistItem(Request $request, Sarpras $sarpras)
    {
        $request->validate([
            'tujuan_periksa' => 'required|string|max:255',
        ]);

        InspectionChecklist::create([
            'sarpras_id'     => $sarpras->id,
            'tujuan_periksa' => $request->tujuan_periksa,
            'is_aktif'       => true,
        ]);

        $this->logActivity('CREATE_CHECKLIST', "Menambah item checklist untuk {$sarpras->nama}");

        return back()->with('success', 'Item checklist berhasil ditambahkan.');
    }

    /**
     * Update a checklist item.
     */
    public function updateChecklistItem(Request $request, InspectionChecklist $checklist)
    {
        $request->validate([
            'tujuan_periksa' => 'required|string|max:255',
        ]);

        $checklist->update([
            'tujuan_periksa' => $request->tujuan_periksa,
        ]);

        $this->logActivity('UPDATE_CHECKLIST', "Mengubah item checklist untuk {$checklist->sarpras->nama}");

        return back()->with('success', 'Item checklist berhasil diperbarui.');
    }

    /**
     * Delete a checklist item.
     */
    public function destroyChecklistItem(InspectionChecklist $checklist)
    {
        $checklist->delete();
        return back()->with('success', 'Item checklist berhasil dihapus.');
    }

    /**
     * Show form to perform inspection on a specific item.
     */
    public function create(SarprasItem $item)
    {
        $sarpras = $item->sarpras;
        $checklists = InspectionChecklist::where('sarpras_id', $sarpras->id)
            ->where('is_aktif', true)
            ->get();

        if ($checklists->isEmpty()) {
            return redirect()->route('admin.sarpras.items.index') // Adjust if needed
                ->with('error', 'Silakan buat checklist untuk sarpras ini terlebih dahulu.');
        }

        return view('pages.admin.inspeksi.create', compact('item', 'checklists'));
    }

    /**
     * Store inspection results.
     */
    public function store(Request $request, SarprasItem $item)
    {
        $request->validate([
            'tanggal_inspeksi' => 'required|date',
            'tipe_inspeksi'    => 'nullable|in:awal,kembali,rutin',
            'peminjaman_id'    => 'nullable|exists:peminjaman,id',
            'results'          => 'required|array',
            'results.*.status' => 'required|in:Baik,Rusak,N/A',
        ]);

        DB::transaction(function () use ($request, $item) {
            $inspection = Inspection::create([
                'sarpras_item_id'  => $item->id,
                'user_id'          => auth()->id(),
                'peminjaman_id'    => $request->peminjaman_id,
                'tipe_inspeksi'    => $request->tipe_inspeksi ?? 'rutin',
                'tanggal_inspeksi' => $request->tanggal_inspeksi,
                'catatan_umum'     => $request->catatan_umum,
            ]);

            foreach ($request->results as $checklistId => $res) {
                InspectionResult::create([
                    'inspection_id'           => $inspection->id,
                    'inspection_checklist_id' => $checklistId,
                    'status'                  => $res['status'],
                    'catatan'                 => $res['catatan'] ?? null,
                ]);
            }
        });

        $tipe = $request->tipe_inspeksi ?? 'rutin';
        $this->logActivity('INSPEKSI_SARPRAS', "Melakukan inspeksi ({$tipe}) untuk item {$item->kode}");

        if ($request->peminjaman_id && $request->tipe_inspeksi === 'kembali') {
             return redirect()->route('admin.peminjaman.riwayat')
                ->with('success', 'Hasil inspeksi pengembalian berhasil disimpan.');
        }

        return redirect()->route('admin.sarpras.items.index')
            ->with('success', 'Hasil inspeksi berhasil disimpan.');
    }

    /**
     * Show form to perform initial inspection on a borrowing.
     */
    public function awalForm(Peminjaman $peminjaman)
    {
        $peminjaman->load(['items.sarprasItem.sarpras.checklists' => function($q) {
            $q->where('is_aktif', true);
        }, 'user']);

        return view('pages.admin.inspeksi.awal_form', compact('peminjaman'));
    }

    /**
     * Store initial inspection results.
     */
    public function storeAwal(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.inspection' => 'required|array',
            'items.*.inspection.*.status' => 'required|in:Baik,Rusak,N/A',
        ]);

        DB::transaction(function () use ($request, $peminjaman) {
            foreach ($request->items as $itemId => $itemData) {
                $inspection = Inspection::create([
                    'sarpras_item_id'  => $itemId,
                    'user_id'          => auth()->id(),
                    'peminjaman_id'    => $peminjaman->id,
                    'tipe_inspeksi'    => 'awal',
                    'tanggal_inspeksi' => now(),
                    'catatan_umum'     => "Inspeksi awal untuk peminjaman {$peminjaman->kode_peminjaman}",
                ]);

                foreach ($itemData['inspection'] as $checklistId => $res) {
                    InspectionResult::create([
                        'inspection_id'           => $inspection->id,
                        'inspection_checklist_id' => $checklistId,
                        'status'                  => $res['status'],
                        'catatan'                 => $res['catatan'] ?? null,
                    ]);
                }
            }
        });

        $this->logActivity('INSPEKSI_SARPRAS_AWAL', "Melakukan inspeksi awal untuk peminjaman {$peminjaman->kode_peminjaman}");

        return redirect()->route('admin.peminjaman.aktif')
            ->with('success', 'Inspeksi awal berhasil disimpan.');
    }

    /**
     * Compare initial and return inspections for a borrowing.
     */
    public function compare(Peminjaman $peminjaman)
    {
        $peminjaman->load(['inspeksiAwal.results.checklist', 'inspeksiKembali.results.checklist', 'user', 'items.sarprasItem.sarpras']);
        
        $awal = $peminjaman->inspeksiAwal;
        $kembali = $peminjaman->inspeksiKembali;

        if (!$awal || !$kembali) {
            return back()->with('error', 'Data inspeksi awal atau kembali tidak lengkap untuk dibandingkan.');
        }

        return view('pages.admin.inspeksi.compare', compact('peminjaman', 'awal', 'kembali'));
    }
}

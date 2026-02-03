<?php

namespace App\Helpers;

use App\Models\Sarpras;

class CodeGenerator
{
    /**
     * Generate kode sarpras berdasarkan kategori, lokasi, dan nama
     * Format: [KATEGORI_CODE][LOKASI_CODE]-[NAMA_5CHAR]-[INCREMENT]
     * Contoh: ELK-LOK1-PROYKTOR-001
     *
     * @param string $kategoriId
     * @param string $lokasiId
     * @param string $nama
     * @return string
     */
    public static function generate(string $kategoriId, string $lokasiId, string $nama): string
    {
        $kategori = \App\Models\KategoriSarpras::find($kategoriId);
        $lokasi = \App\Models\Lokasi::find($lokasiId);

        if (!$kategori || !$lokasi) {
            throw new \Exception('Kategori atau Lokasi tidak ditemukan');
        }

        // Ambil 3 karakter pertama dari kategori (uppercase)
        $kategoriCode = strtoupper(substr($kategori->nama, 0, 3));

        // Ambil 3 karakter pertama dari lokasi (uppercase)
        $lokasiCode = strtoupper(substr($lokasi->nama, 0, 3));

        // Ambil 5 karakter pertama dari nama (uppercase)
        $namaCode = strtoupper(substr($nama, 0, 5));

        // Cari increment untuk kombinasi ini
        $baseCode = "{$kategoriCode}-{$lokasiCode}-{$namaCode}";

        // Cari semua kode yang dimulai dengan base code ini
        $lastCode = Sarpras::where('kode', 'LIKE', $baseCode . '-%')
            ->orderBy('kode', 'DESC')
            ->first();

        if ($lastCode) {
            // Ekstrak nomor dari kode terakhir
            preg_match('/(\d+)$/', $lastCode->kode, $matches);
            $increment = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        } else {
            $increment = 1;
        }

        // Format dengan padding 3 digit
        $code = "{$baseCode}-" . str_pad($increment, 3, '0', STR_PAD_LEFT);

        return $code;
    }
}

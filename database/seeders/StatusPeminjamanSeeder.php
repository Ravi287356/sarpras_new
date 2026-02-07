<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusPeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            // Status ketersediaan barang
            'tersedia',
            'butuh maintenance',

            // Status proses peminjaman
            'menunggu persetujuan',
            'disetujui',
            'ditolak',

            // Status peminjaman aktif
            'dipinjam',
            'terlambat',

            // Status hasil peminjaman
            'dikembalikan',
            'hilang',
        ];

        foreach ($statuses as $status) {
            DB::table('status_peminjaman')->updateOrInsert(
                ['nama' => $status], // cek UNIQUE
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

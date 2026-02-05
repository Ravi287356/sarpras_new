<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Tambah kolom baru (tidak mengganggu status)
        Schema::table('peminjaman', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman', 'tujuan')) {
                $table->text('tujuan')->nullable()->after('jumlah');
            }

            if (!Schema::hasColumn('peminjaman', 'approved_by')) {
                $table->char('approved_by', 36)->nullable()->after('status');
            }

            if (!Schema::hasColumn('peminjaman', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('peminjaman', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('approved_at');
            }
        });

        // 2) Ubah ENUM - hanya untuk MySQL (SQLite tidak support MODIFY)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE peminjaman
                MODIFY status ENUM(
                    'dipinjam',
                    'menunggu',
                    'disetujui',
                    'ditolak',
                    'dikembalikan'
                ) NOT NULL DEFAULT 'menunggu'
            ");

            // 3) Konversi data lama (sekarang 'disetujui' sudah valid)
            DB::statement("
                UPDATE peminjaman
                SET status = 'disetujui'
                WHERE status = 'dipinjam'
            ");

            // 4) Ubah ENUM ke versi FINAL (hapus 'dipinjam')
            DB::statement("
                ALTER TABLE peminjaman
                MODIFY status ENUM(
                    'menunggu',
                    'disetujui',
                    'ditolak',
                    'dikembalikan'
                ) NOT NULL DEFAULT 'menunggu'
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // 1) Kembalikan enum supaya bisa nampung nilai 'dipinjam' lagi
            DB::statement("
                ALTER TABLE peminjaman
                MODIFY status ENUM(
                    'dipinjam',
                    'dikembalikan'
                ) NOT NULL DEFAULT 'dipinjam'
            ");
        }

        // 2) Hapus kolom tambahan
        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'tujuan')) $table->dropColumn('tujuan');
            if (Schema::hasColumn('peminjaman', 'approved_by')) $table->dropColumn('approved_by');
            if (Schema::hasColumn('peminjaman', 'approved_at')) $table->dropColumn('approved_at');
            if (Schema::hasColumn('peminjaman', 'alasan_penolakan')) $table->dropColumn('alasan_penolakan');
        });
    }
};

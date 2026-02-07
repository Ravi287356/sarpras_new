<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sarpras', function (Blueprint $table) {
            // drop unique index on kode if exists

            if (Schema::hasColumn('sarpras', 'lokasi_id')) {
                $table->dropColumn('lokasi_id');
            }
            if (Schema::hasColumn('sarpras', 'jumlah_stok')) {
                $table->dropColumn('jumlah_stok');
            }
            if (Schema::hasColumn('sarpras', 'kondisi_saat_ini')) {
                $table->dropColumn('kondisi_saat_ini');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sarpras', function (Blueprint $table) {
            $table->uuid('lokasi_id')->nullable()->after('kategori_id');
            $table->integer('jumlah_stok')->default(0)->after('lokasi_id');
            $table->string('kondisi_saat_ini')->nullable()->after('jumlah_stok');
        });
    }
};

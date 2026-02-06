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
        Schema::table('pengaduan', function (Blueprint $table) {
            // add nullable foreign keys to lokasi and kategori_sarpras
            $table->string('lokasi_id')->nullable()->after('lokasi');
            $table->string('kategori_id')->nullable()->after('lokasi_id');

            $table->foreign('lokasi_id')
                  ->references('id')
                  ->on('lokasi')
                  ->nullOnDelete();

            $table->foreign('kategori_id')
                  ->references('id')
                  ->on('kategori_sarpras')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropForeign(['lokasi_id']);
            $table->dropForeign(['kategori_id']);
            $table->dropColumn(['lokasi_id', 'kategori_id']);
        });
    }
};

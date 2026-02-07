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
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->unsignedBigInteger('status_peminjaman_id')->nullable()->after('tanggal_kembali_actual');
            $table->foreign('status_peminjaman_id')
                  ->references('id')
                  ->on('status_peminjaman')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['status_peminjaman_id']);
            $table->dropColumn('status_peminjaman_id');
        });
    }
};

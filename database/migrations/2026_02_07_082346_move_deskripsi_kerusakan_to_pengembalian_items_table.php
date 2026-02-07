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
        Schema::table('pengembalian_items', function (Blueprint $table) {
            $table->text('deskripsi_kerusakan')->nullable()->after('foto_url');
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn('deskripsi_kerusakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->text('deskripsi_kerusakan')->nullable()->after('tanggal_pengembalian');
        });

        Schema::table('pengembalian_items', function (Blueprint $table) {
            $table->dropColumn('deskripsi_kerusakan');
        });
    }
};

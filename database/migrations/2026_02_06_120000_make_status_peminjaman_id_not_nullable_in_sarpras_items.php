<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set default value for existing records (tersedia status)
        $tersediaStatus = DB::table('status_peminjaman')->where('nama', 'tersedia')->first();
        if ($tersediaStatus) {
            DB::table('sarpras_items')
                ->whereNull('status_peminjaman_id')
                ->update(['status_peminjaman_id' => $tersediaStatus->id]);
        }

        // Drop the old foreign key constraint
        Schema::table('sarpras_items', function (Blueprint $table) {
            $table->dropForeign('sarpras_items_status_peminjaman_id_foreign');
        });

        // Make the column NOT NULLABLE and recreate foreign key with restrictOnDelete
        Schema::table('sarpras_items', function (Blueprint $table) {
            $table->unsignedBigInteger('status_peminjaman_id')->nullable(false)->change();
            $table->foreign('status_peminjaman_id')
                  ->references('id')
                  ->on('status_peminjaman')
                  ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new foreign key constraint
        Schema::table('sarpras_items', function (Blueprint $table) {
            $table->dropForeign('sarpras_items_status_peminjaman_id_foreign');
        });

        // Restore nullable column and old foreign key with nullOnDelete
        Schema::table('sarpras_items', function (Blueprint $table) {
            $table->unsignedBigInteger('status_peminjaman_id')->nullable()->change();
            $table->foreign('status_peminjaman_id')
                  ->references('id')
                  ->on('status_peminjaman')
                  ->nullOnDelete();
        });
    }
};

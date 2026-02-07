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
        Schema::table('pengembalian', function (Blueprint $table) {
            // Add new columns
            $table->foreignId('kondisi_alat_id')->nullable()->constrained('kondisi_alat')->nullOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_petugas')->nullable();

            // Drop old string column if it exists
            if (Schema::hasColumn('pengembalian', 'kondisi_alat')) {
                $table->dropColumn('kondisi_alat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropForeign(['kondisi_alat_id']);
            $table->dropColumn('kondisi_alat_id');

            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');

            $table->dropColumn('catatan_petugas');

            // Restore old column
            $table->string('kondisi_alat')->nullable();
        });
    }
};

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
            if (Schema::hasColumn('peminjaman', 'sarpras_id')) {
                // drop foreign key first
                try {
                    $table->dropForeign(['sarpras_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('sarpras_id');
            }

            if (Schema::hasColumn('peminjaman', 'jumlah')) {
                $table->dropColumn('jumlah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreignUuid('sarpras_id')->nullable()->after('user_id')->constrained('sarpras')->cascadeOnDelete();
            $table->integer('jumlah')->default(1)->after('sarpras_id');
        });
    }
};

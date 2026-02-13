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
        // 1. Checklist Templates (linked to Sarpras type/header)
        Schema::create('inspection_checklists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sarpras_id')->constrained('sarpras')->cascadeOnDelete();
            $table->string('tujuan_periksa'); // e.g., "Cek Layar", "Cek Keyboard"
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Inspection Events (linked to individual Sarpras Item)
        Schema::create('inspections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sarpras_item_id')->constrained('sarpras_items')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('peminjaman_id')->nullable()->constrained('peminjaman')->nullOnDelete();
            $table->string('tipe_inspeksi')->default('rutin'); // 'awal', 'kembali', 'rutin'
            $table->timestamp('tanggal_inspeksi');
            $table->text('catatan_umum')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Individual Checklist Results for each inspection
        Schema::create('inspection_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('inspection_id')->constrained('inspections')->cascadeOnDelete();
            $table->foreignUuid('inspection_checklist_id')->constrained('inspection_checklists')->cascadeOnDelete();
            $table->string('status'); // e.g., "Baik", "Rusak", "N/A"
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_results');
        Schema::dropIfExists('inspections');
        Schema::dropIfExists('inspection_checklists');
    }
};

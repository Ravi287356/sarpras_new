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
        Schema::create('sarpras_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('sarpras_id')->constrained('sarpras')->cascadeOnDelete();
            $table->string('kode')->unique();
            $table->foreignUuid('lokasi_id')->nullable()->constrained('lokasi')->nullOnDelete();
            $table->unsignedBigInteger('kondisi_alat_id')->nullable();
            $table->unsignedBigInteger('status_peminjaman_id')->nullable();
            $table->timestamps();

            $table->foreign('kondisi_alat_id')
                  ->references('id')
                  ->on('kondisi_alat')
                  ->nullOnDelete();

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
        Schema::dropIfExists('sarpras_items');
    }
};

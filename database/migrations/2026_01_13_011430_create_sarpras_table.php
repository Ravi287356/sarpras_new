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
        Schema::create('sarpras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string(column: 'kode')->unique();
            $table->string(column: 'nama');
            $table->uuid(column: 'kategori_id');
            $table->foreign('kategori_id')
                    ->references('id')
                    ->on('kategori_sarpras')
                    ->cascadeOnDelete();
            $table->uuid(column: 'lokasi_id')->nullable();
            $table->integer(column: 'jumlah_stok')->default(value: 0);
            $table->string(column: 'kondisi_saat_ini')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sarpras');
    }
};

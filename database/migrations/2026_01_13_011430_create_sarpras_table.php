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
            $table->id();
            $table->string(column: 'kode')->unique();
            $table->string(column: 'nama');
            $table->foreignId(column: 'kategori_id')
                    ->constrained(table: 'kategori_sarpras')
                    ->cascadeOnDelete();
            $table->string(column: 'lokasi')->nullable();
            $table->integer(column: 'jumlah_stok')->default(value: 0);
            $table->string(column: 'kondisi_saat_ini')->nullable();
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

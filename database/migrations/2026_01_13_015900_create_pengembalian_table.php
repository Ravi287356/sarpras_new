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
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'peminjaman_id')
                    ->constrained(table: 'peminjaman')
                    ->cascadeOnDelete();
            $table->date(column: 'tanggal_pengembalian')->default(value: now());
            $table->string(column: 'kondisi_alat')->nullable();
            $table->string(column: 'deskripsi_kerusakan')->nullable();
            $table->string(column: 'foto_url')->default(value: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');}
};

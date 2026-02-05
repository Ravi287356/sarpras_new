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
            $table->uuid('id')->primary();
            $table->foreignUuid(column: 'peminjaman_id')
                    ->constrained(table: 'peminjaman')
                    ->cascadeOnDelete();
            $table->timestamp(column: 'tanggal_pengembalian')->useCurrent();
            $table->string(column: 'kondisi_alat')->nullable();
            $table->text(column: 'deskripsi_kerusakan')->nullable();
            $table->text(column: 'foto_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};

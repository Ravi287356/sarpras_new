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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid(column: 'user_id')
                    ->constrained(table: 'users')
                    ->cascadeOnDelete();
            $table->string(column: 'judul');
            $table->text(column: 'deskripsi');
            $table->string(column: 'lokasi');
            $table->enum(column: 'status', allowed: ['Belum Ditindaklanjuti', 'Sedang Diproses', 'Selesai', 'Ditutup'])->default(value: 'Belum Ditindaklanjuti');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};

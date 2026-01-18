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
        Schema::create('peminjaman', callback: function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'user_id')
                    ->constrained(table: 'users')
                    ->cascadeOnDelete();
            $table->foreignId(column: 'sarpras_id')
                    ->constrained(table: 'sarpras')
                    ->cascadeOnDelete();
            $table->integer(column: 'jumlah')->default(value: 0);
            $table->date(column: 'tanggal_pinjam')->default(value: now());
            $table->date(column: 'tanggal_kembali_rencana')->nullable();
            $table->date(column: 'tanggal_kembali_actual')->nullable();
            $table->enum(column: 'status', allowed: ['dipinjam', 'dikembalikan', 'terlambat'])->default(value: 'dipinjam');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};

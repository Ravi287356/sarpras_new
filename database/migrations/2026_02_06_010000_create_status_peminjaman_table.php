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
        Schema::create('status_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        // seed basic statuses
        if (app()->runningInConsole()) {
            DB::table('status_peminjaman')->insert([
                ['nama' => 'tersedia', 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'menunggu persetujuan', 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'dipinjam', 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'disetujui', 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'ditolak', 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'dikembalikan', 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'terlambat', 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'hilang', 'created_at' => now(), 'updated_at' => now()],
                ['nama' => 'butuh maintenance', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_peminjaman');
    }
};

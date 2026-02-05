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
        Schema::create('catatan_pengaduan', callback: function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'pengaduan_id')
                    ->constrained(table: 'pengaduan')
                    ->cascadeOnDelete();
            $table->foreignUuid(column: 'user_id')
                    ->constrained(table: 'users')
                    ->cascadeOnDelete();
            $table->string(column: 'catatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_pengaduan');
    }
};

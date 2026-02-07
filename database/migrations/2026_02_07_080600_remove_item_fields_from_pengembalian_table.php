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
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropForeign(['kondisi_alat_id']);
            $table->dropColumn(['kondisi_alat_id', 'foto_url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->unsignedBigInteger('kondisi_alat_id')->nullable();
            $table->string('foto_url')->nullable();
            
            $table->foreign('kondisi_alat_id')
                ->references('id')
                ->on('kondisi_alat')
                ->onDelete('restrict');
        });
    }
};

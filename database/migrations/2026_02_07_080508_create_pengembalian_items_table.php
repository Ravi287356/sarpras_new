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
        Schema::create('pengembalian_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pengembalian_id');
            $table->uuid('sarpras_item_id');
            $table->unsignedBigInteger('kondisi_alat_id');
            $table->string('foto_url', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('pengembalian_id')
                ->references('id')
                ->on('pengembalian')
                ->onDelete('cascade');

            $table->foreign('sarpras_item_id')
                ->references('id')
                ->on('sarpras_items')
                ->onDelete('cascade');

            $table->foreign('kondisi_alat_id')
                ->references('id')
                ->on('kondisi_alat')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_items');
    }
};

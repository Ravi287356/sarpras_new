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
        // Add foreign key constraint for approved_by
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreign('approved_by')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
        });
    }
};

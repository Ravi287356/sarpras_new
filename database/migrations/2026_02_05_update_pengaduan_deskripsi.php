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
        // MySQL doesn't support MODIFY in SQLite, skip for SQLite
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            Schema::table('pengaduan', function (Blueprint $table) {
                // Change deskripsi from string(255) to text to support long descriptions + appended notes
                $table->text('deskripsi')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->string('deskripsi')->change();
        });
    }
};

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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignid(column:'user_id')
                    ->contrained(table: 'users')
                    ->cascadeOnDelete();
            $table->string(column: 'aksi');
            $table->string(column: 'deskripsi');
            $table->timestamp(column: 'timesatamp')->default(value: now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};

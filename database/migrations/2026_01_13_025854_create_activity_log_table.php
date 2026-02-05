<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('aksi');
            $table->text('deskripsi')->nullable();

            $table->timestamp('timestamp')->useCurrent();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

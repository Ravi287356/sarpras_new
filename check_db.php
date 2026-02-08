<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StatusPeminjaman;
use App\Models\KondisiAlat;

echo "--- Status Peminjaman ---\n";
foreach(StatusPeminjaman::all() as $s) {
    echo "- [{$s->id}] {$s->nama}\n";
}

echo "\n--- Kondisi Alat ---\n";
foreach(KondisiAlat::all() as $k) {
    echo "- [{$k->id}] {$k->nama}\n";
}

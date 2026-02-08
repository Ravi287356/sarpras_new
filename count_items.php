<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SarprasItem;
use App\Models\StatusPeminjaman;

echo "--- Items Count per Status ---\n";
foreach(StatusPeminjaman::all() as $s) {
    $count = SarprasItem::where('status_peminjaman_id', $s->id)->count();
    echo "- [{$s->id}] {$s->nama}: {$count} items\n";
}

echo "\n--- Items Count per Condition ---\n";
foreach(App\Models\KondisiAlat::all() as $k) {
    $count = SarprasItem::where('kondisi_alat_id', $k->id)->count();
    echo "- [{$k->id}] {$k->nama}: {$count} items\n";
}

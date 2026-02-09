<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\KondisiAlat;
use App\Models\Sarpras;
use App\Models\SarprasItem;


$output = "";

$conditions = KondisiAlat::all();
foreach ($conditions as $c) {
    $currentCount = SarprasItem::where('kondisi_alat_id', $c->id)->count();
    $historyCount = \App\Models\PengembalianItem::where('kondisi_alat_id', $c->id)->count();
    $output .= "Condition: '{$c->nama}' (ID: {$c->id})\n";
    $output .= "  - Current Items: {$currentCount}\n";
    $output .= "  - History Returns: {$historyCount}\n";
}

$output .= "\n--- BY STATUS PEMINJAMAN ---\n";
// Check status maintenance
$maintenanceStatus = \App\Models\StatusPeminjaman::where('nama', 'like', '%maintenance%')->get();
foreach ($maintenanceStatus as $s) {
    $count = SarprasItem::where('status_peminjaman_id', $s->id)->count();
    $output .= "Status: '{$s->nama}' (ID: {$s->id}) -> Item Count: {$count}\n";
}

$output .= "\n--- RAW QUERY CHECK ---\n";
// Let's pick one Sarpras that supposedly has broken items
$testItem = Sarpras::first();
if ($testItem) {
    $output .= "Testing Sarpras: {$testItem->nama} (ID: {$testItem->id})\n";
    $items = $testItem->items;
    foreach ($items as $i) {
        $cName = $i->kondisi->nama ?? 'null';
        $sName = $i->statusPeminjaman->nama ?? 'null';
        $output .= "- Item ID: {$i->id} | Kondisi: {$cName} | Status: {$sName}\n";
    }
}

file_put_contents(__DIR__ . '/debug_result.txt', $output);

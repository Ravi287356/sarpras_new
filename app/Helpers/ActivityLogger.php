<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('activity_log')) {
    function activity_log(string $aksi, string $deskripsi = null)
    {
        ActivityLog::create([
            'user_id'   => Auth::id(),
            'aksi'      => $aksi,
            'deskripsi' => $deskripsi,
        ]);
    }
}

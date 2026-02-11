<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Log activity dengan user_id, aksi, dan deskripsi
     */
    protected function logActivity(string $aksi, ?string $deskripsi = null): void
    {
        try {
            ActivityLog::create([
                'user_id'    => auth()->check() ? auth()->id() : null,
                'aksi'       => $aksi,
                'ip_address' => request()->ip(),
                'metadata'   => [
                    'user_agent' => request()->header('User-Agent'),
                ],
                'deskripsi'  => $deskripsi,
            ]);
        } catch (\Throwable $e) {
            // jangan crash aplikasi
        }
    }

    /**
     * Format "siapa dan role"
     */
    protected function whoPrefixed(string $prefix, ?string $extra = null): string
    {
        $username = auth()->user()?->username ?? '-';
        $role     = auth()->user()?->role?->nama ?? '-';
        $text     = "{$prefix} oleh {$username} ({$role})";
        return $extra ? "{$text} - {$extra}" : $text;
    }
}


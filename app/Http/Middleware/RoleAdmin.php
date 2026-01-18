<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login.form');
        }

        $roleNama = auth()->user()->role->nama ?? null;

        // ✅ kalau role User, tidak boleh akses kelola user
        if ($roleNama === 'User') {
            abort(403, 'User tidak punya akses kelola user.');
        }

        return $next($request);
    }
}

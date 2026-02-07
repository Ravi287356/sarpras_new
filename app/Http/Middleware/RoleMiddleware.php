<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Middleware: role:admin / role:operator / role:user
     * Mendukung multiple roles: role:admin|operator
     * + BONUS: pisahkan session cookie per area (admin/operator/user)
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {

        $area = $request->segment(1); // admin/operator/user

        if (in_array($area, ['admin', 'operator', 'user'], true)) {
            config(['session.cookie' => "sarpras_{$area}_session"]);
        } else {

            config(['session.cookie' => "sarpras_session"]);
        }

        if (!Auth::check()) {
            return redirect()->route('login.form');
        }

        $userRole = Auth::user()?->role?->nama;

        // Mendukung multiple roles dengan separator |
        $allowedRoles = explode('|', $role);

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}

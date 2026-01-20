<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
public function handle(Request $request, Closure $next, ...$roles): Response{
    if (!Auth::check()) {
        return redirect()->route('login');}

        $user = Auth::user();
        $userRole = $user->role->nama;

        if (!in_array($userRole, $roles)) {
            abort(403, 'Anda tidak memiliki akses');
        }

        return $next($request);
    }
}

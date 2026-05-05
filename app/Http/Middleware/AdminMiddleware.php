<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = session('user');
        if (!$user || !in_array($user->role, ['admin', 'admin_galangan'])) {
            return redirect('/dashboard')->with('error', 'Akses ditolak. Anda bukan admin.');
        }

        return $next($request);
    }
}

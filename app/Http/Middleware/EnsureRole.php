<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }
        if ($user->role === $role) {
            return $next($request);
        }
        if ($user->role === 'professor') {
            return redirect()->route('professor.dashboard');
        }
        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        abort(403, 'Unauthorized.');
    }
}

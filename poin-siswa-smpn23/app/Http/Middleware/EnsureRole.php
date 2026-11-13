<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $allowed = [];

        foreach ($roles as $role) {
            $allowed = array_merge($allowed, explode(',', $role));
        }

        $allowed = array_filter($allowed);

        if (! empty($allowed) && ! auth()->user()->hasAnyRole($allowed)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}

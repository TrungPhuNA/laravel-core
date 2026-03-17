<?php

namespace App\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class ForceJsonAccept
{
    public function handle(Request $request, Closure $next)
    {
        // Many clients send only Content-Type without Accept, which makes Laravel treat
        // unauthenticated requests as "web" and attempt redirects to route('login').
        if ($request->is('api/*')) {
            $accept = (string) $request->header('Accept', '');

            if ($accept === '' || !str_contains($accept, 'application/json')) {
                $request->headers->set('Accept', 'application/json');
            }
        }

        return $next($request);
    }
}


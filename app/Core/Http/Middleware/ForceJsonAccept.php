<?php

namespace App\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class ForceJsonAccept
{
    public function handle(Request $request, Closure $next)
    {
        // Nhieu client chi gui Content-Type ma khong gui Accept, lam Laravel hieu sai thanh web request,
        // dan den auth middleware co the redirect ve route('login') va tra ve HTML.
        if ($request->is('api/*')) {
            $accept = (string) $request->header('Accept', '');

            if ($accept === '' || !str_contains($accept, 'application/json')) {
                $request->headers->set('Accept', 'application/json');
            }
        }

        return $next($request);
    }
}

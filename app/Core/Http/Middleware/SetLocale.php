<?php

namespace App\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class SetLocale
{
    /**
     * Cac ngon ngu ho tro cho thong bao loi/validation.
     */
    private const SUPPORTED = ['vi', 'en'];

    public function handle(Request $request, Closure $next)
    {
        $locale = $this->resolveLocale($request);

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    private function resolveLocale(Request $request): ?string
    {
        // Thu tu uu tien: query -> X-Locale -> Accept-Language
        $candidate = $request->query('lang')
            ?: $request->header('X-Locale')
            ?: $this->fromAcceptLanguage($request->header('Accept-Language'));

        if (!$candidate) {
            return null;
        }

        $candidate = strtolower(trim((string) $candidate));

        // Chuan hoa: vi-VN -> vi, en-US -> en
        if (str_contains($candidate, ',')) {
            $candidate = explode(',', $candidate, 2)[0];
        }

        if (str_contains($candidate, ';')) {
            $candidate = explode(';', $candidate, 2)[0];
        }

        if (str_contains($candidate, '-')) {
            $candidate = explode('-', $candidate, 2)[0];
        }

        $candidate = substr($candidate, 0, 2);

        return in_array($candidate, self::SUPPORTED, true) ? $candidate : null;
    }

    private function fromAcceptLanguage(?string $acceptLanguage): ?string
    {
        return $acceptLanguage ? (string) $acceptLanguage : null;
    }
}

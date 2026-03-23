<?php

namespace App\Core\Http\Middleware;

use Closure;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gắn thời gian xử lý request (ms) vào response API.
 *
 * - Body: meta.duration_ms
 * - Header: X-Response-Time-Ms
 */
final class ResponseTime
{
    public const META_KEY = 'duration_ms';
    public const HEADER_KEY = 'X-Response-Time-Ms';
    public const ATTRIBUTE_START_NS = 'core.request_start_ns';

    public function handle(Request $request, Closure $next): Response
    {
        $startNs = hrtime(true);
        $request->attributes->set(self::ATTRIBUTE_START_NS, $startNs);

        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            /** @var ExceptionHandler $handler */
            $handler = app(ExceptionHandler::class);
            $handler->report($e);
            $response = $handler->render($request, $e);
        }

        $durationMs = (int) round((hrtime(true) - $startNs) / 1_000_000);
        $response->headers->set(self::HEADER_KEY, (string) $durationMs);

        if ($response instanceof JsonResponse) {
            $payload = $response->getData(true);
            if (is_array($payload)) {
                $meta = $payload['meta'] ?? [];
                $meta = is_array($meta) ? $meta : [];

                $meta[self::META_KEY] = $durationMs;
                $payload['meta'] = $meta;

                $response->setData($payload);
            }
        }

        return $response;
    }
}


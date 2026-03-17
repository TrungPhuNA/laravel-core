<?php

namespace App\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gắn request id (trace id) cho mỗi request để debug log và trace giữa các service.
 *
 * Quy ước:
 * - Client có thể gửi `X-Request-Id` hoặc `X-Correlation-Id` hoặc `correlation_id`.
 * - Nếu không có, server sẽ tự generate.
 * - Server luôn trả về `X-Request-Id` trong response.
 */
final class RequestId
{
    public const ATTRIBUTE_KEY = 'core.request_id';
    public const HEADER_KEY = 'X-Request-Id';

    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $this->extractRequestId($request) ?: (string) Str::ulid();

        // Lưu vào attributes để code nội bộ (client gọi microservice, log, response...) dùng lại.
        $request->attributes->set(self::ATTRIBUTE_KEY, $requestId);

        $response = $next($request);

        // Đảm bảo response luôn có trace id.
        $response->headers->set(self::HEADER_KEY, $requestId);

        return $response;
    }

    private function extractRequestId(Request $request): ?string
    {
        $candidates = [
            $request->header('X-Request-Id'),
            $request->header('X-Correlation-Id'),
            $request->header('Correlation-Id'),
            $request->header('correlation_id'),
            $request->header('X-Trace-Id'),
        ];

        foreach ($candidates as $value) {
            $value = is_string($value) ? trim($value) : '';
            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }
}


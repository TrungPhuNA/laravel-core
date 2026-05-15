<?php

namespace App\Core\Http\Responses;

use App\Core\Support\Pagination\PaginationMeta;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use App\Core\Http\Middleware\RequestId;

final class ApiResponse
{
    private static function traceId(): ?string
    {
        try {
            $request = request();
        } catch (\Throwable) {
            return null;
        }

        $id = $request->attributes->get(RequestId::ATTRIBUTE_KEY)
            ?? $request->header(RequestId::HEADER_KEY)
            ?? $request->header('X-Correlation-Id')
            ?? $request->header('correlation_id');

        $id = is_string($id) ? trim($id) : '';

        return $id !== '' ? $id : null;
    }

    /**
     * JSend: success (thanh cong)
     */
    public static function success(mixed $data = null, string $code = 'SUCCESS', string $message = 'OK', int $status = 200, ?array $meta = null): JsonResponse
    {
        $payload = [
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];

        if ($meta !== null) {
            $payload['meta'] = $meta;
        }

        if ($traceId = self::traceId()) {
            $payload['trace_id'] = $traceId;
        }

        return response()->json($payload, $status, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public static function paginated(LengthAwarePaginator $paginator, mixed $items, string $code = 'SUCCESS', string $message = 'OK', int $status = 200): JsonResponse
    {
        return self::success(
            data: ['items' => $items],
            code: $code,
            message: $message,
            status: $status,
            meta: PaginationMeta::fromLengthAwarePaginator($paginator),
        );
    }

    /**
     * JSend: fail (loi phia client: validation, precondition, ...).
     *
     * @param array<string, mixed> $data
     */
    public static function fail(array $data, string $code = 'FAIL', string $message = 'Request failed', int $status = 400, ?array $meta = null): JsonResponse
    {
        $payload = [
            'status' => 'fail',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];

        if ($meta !== null) {
            $payload['meta'] = $meta;
        }

        if ($traceId = self::traceId()) {
            $payload['trace_id'] = $traceId;
        }

        return response()->json($payload, $status, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * JSend: error (loi phia server).
     *
     * @param array<string, mixed>|null $data
     */
    public static function error(string $message = 'Server error', ?string $code = 'ERROR', ?array $data = null, int $status = 500, ?array $meta = null): JsonResponse
    {
        $payload = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($code !== null) {
            $payload['code'] = $code;
        }

        if ($data !== null) {
            $payload['data'] = $data;
        }

        if ($meta !== null) {
            $payload['meta'] = $meta;
        }

        if ($traceId = self::traceId()) {
            $payload['trace_id'] = $traceId;
        }

        return response()->json($payload, $status, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}

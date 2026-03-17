<?php

namespace App\Core\Http\Responses;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    public static function ok(mixed $data = null, array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => array_merge(self::baseMeta(), $meta),
        ], $status);
    }

    public static function error(string $code, string $message, array $details = [], array $meta = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ],
            'meta' => array_merge(self::baseMeta(), $meta),
        ], $status);
    }

    /**
     * @return array<string, mixed>
     */
    private static function baseMeta(): array
    {
        $requestId = request()?->header('X-Request-Id');

        return [
            'request_id' => $requestId,
        ];
    }
}


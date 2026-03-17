<?php

namespace App\Core\Http\Responses;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    /**
     * JSend: success (thanh cong)
     */
    public static function success(mixed $data = null, string $code = 'SUCCESS', string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * JSend: fail (loi phia client: validation, precondition, ...).
     *
     * @param array<string, mixed> $data
     */
    public static function fail(array $data, string $code = 'FAIL', string $message = 'Request failed', int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'fail',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * JSend: error (loi phia server).
     *
     * @param array<string, mixed>|null $data
     */
    public static function error(string $message = 'Server error', ?string $code = 'ERROR', ?array $data = null, int $status = 500): JsonResponse
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

        return response()->json($payload, $status);
    }
}

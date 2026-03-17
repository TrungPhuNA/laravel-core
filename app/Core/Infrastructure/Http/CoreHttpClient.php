<?php

namespace App\Core\Infrastructure\Http;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Http\Middleware\RequestId;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Wrapper chuẩn để gọi microservice.
 *
 * Tính năng:
 * - Timeout + connect timeout
 * - Retry (khi lỗi tạm thời)
 * - Header chuẩn: Accept JSON, X-Request-Id, X-Locale, User-Agent
 * - Log request/response (có mask dữ liệu nhạy cảm)
 * - Map lỗi về ApiException để luôn ra chuẩn response của core
 */
final class CoreHttpClient
{
    public function __construct(
        private readonly string $serviceName,
        private readonly string $baseUrl,
        /** @var array<string, string> */
        private readonly array $defaultHeaders = [],
    ) {}

    /**
     * Request JSON và trả về decoded array.
     *
     * @return array<string, mixed>
     */
    public function requestJson(string $method, string $uri, array $data = [], array $headers = []): array
    {
        $response = $this->request($method, $uri, $data, $headers);

        $json = $response->json();

        if (!is_array($json)) {
            throw new ApiException(
                errorCode: ErrorCode::MICROSERVICE_BAD_RESPONSE->value,
                message: __('messages.microservice_bad_response'),
                status: 502,
                details: [
                    'service' => $this->serviceName,
                ],
            );
        }

        /** @var array<string, mixed> $json */
        return $json;
    }

    /**
     * Request và trả về raw Response (khi cần status/header...).
     */
    public function request(string $method, string $uri, array $data = [], array $headers = []): Response
    {
        $method = strtoupper(trim($method));

        $timeout = (float) config('core.http.timeout', 10);
        $connectTimeout = (float) config('core.http.connect_timeout', 3);
        $retryTimes = (int) config('core.http.retry.times', 2);
        $retrySleepMs = (int) config('core.http.retry.sleep_ms', 200);

        $pending = Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson()
            ->timeout($timeout)
            ->connectTimeout($connectTimeout)
            ->withHeaders($this->buildHeaders($headers))
            ->retry(
                times: $retryTimes + 1,
                sleepMilliseconds: $retrySleepMs,
                when: function (\Throwable $e) {
                    if ($e instanceof ConnectionException) {
                        return true;
                    }

                    if ($e instanceof RequestException) {
                        $status = $e->response?->status();
                        return is_int($status) && ($status >= 500 || $status === 429);
                    }

                    return false;
                },
                throw: false,
            );

        $options = $this->buildOptions($method, $data);
        $start = microtime(true);

        try {
            $response = $pending->send($method, $uri, $options);
        } catch (ConnectionException $e) {
            $this->logFailure($method, $uri, $options, null, $start, $e);

            throw new ApiException(
                errorCode: ErrorCode::MICROSERVICE_UNAVAILABLE->value,
                message: __('messages.microservice_unavailable'),
                status: 502,
                details: [
                    'service' => $this->serviceName,
                ],
            );
        } catch (\Throwable $e) {
            $this->logFailure($method, $uri, $options, null, $start, $e);

            throw new ApiException(
                errorCode: ErrorCode::MICROSERVICE_ERROR->value,
                message: __('messages.microservice_error'),
                status: 502,
                details: [
                    'service' => $this->serviceName,
                ],
            );
        }

        $this->logResponse($method, $uri, $options, $response, $start);

        if ($response->successful()) {
            return $response;
        }

        // Microservice tra ve non-2xx: coi nhu upstream error.
        throw new ApiException(
            errorCode: ErrorCode::MICROSERVICE_ERROR->value,
            message: __('messages.microservice_error'),
            status: 502,
            details: [
                'service' => $this->serviceName,
                'remote_status' => $response->status(),
            ],
        );
    }

    /**
     * @return array<string, string>
     */
    private function buildHeaders(array $headers): array
    {
        $base = [
            'User-Agent' => (string) config('core.http.headers.user_agent', 'laravel-core-api'),
        ];

        $locale = app()->getLocale();
        if (is_string($locale) && $locale !== '') {
            $base['X-Locale'] = $locale;
        }

        $traceId = $this->currentTraceId();
        if ($traceId !== null) {
            $base[RequestId::HEADER_KEY] = $traceId;
        }

        return array_merge($base, $this->defaultHeaders, $headers);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildOptions(string $method, array $data): array
    {
        if ($method === 'GET') {
            return ['query' => $data];
        }

        // POST/PUT/PATCH/DELETE...: mac dinh gui JSON body.
        return ['json' => $data];
    }

    private function currentTraceId(): ?string
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

    private function logResponse(string $method, string $uri, array $options, Response $response, float $start): void
    {
        if (!(bool) config('core.http.log.enabled', true)) {
            return;
        }

        $ms = (int) round((microtime(true) - $start) * 1000);

        Log::info('microservice.response', [
            'service' => $this->serviceName,
            'method' => $method,
            'uri' => $this->sanitizeUrl($uri),
            'status' => $response->status(),
            'duration_ms' => $ms,
            'trace_id' => $this->currentTraceId(),
            'request' => $this->sanitizeBody($this->extractBodyForLog($options)),
            'response' => $this->sanitizeBody($this->extractResponseBodyForLog($response)),
        ]);
    }

    private function logFailure(string $method, string $uri, array $options, ?Response $response, float $start, \Throwable $e): void
    {
        if (!(bool) config('core.http.log.enabled', true)) {
            return;
        }

        $ms = (int) round((microtime(true) - $start) * 1000);

        Log::warning('microservice.failure', [
            'service' => $this->serviceName,
            'method' => $method,
            'uri' => $this->sanitizeUrl($uri),
            'duration_ms' => $ms,
            'trace_id' => $this->currentTraceId(),
            'request' => $this->sanitizeBody($this->extractBodyForLog($options)),
            'response_status' => $response?->status(),
            'exception' => get_class($e),
            'message' => $e->getMessage(),
        ]);
    }

    private function sanitizeUrl(string $uri): string
    {
        // Tranh log query co the chua data nhay cam.
        return explode('?', $uri, 2)[0];
    }

    /**
     * @return array<string, mixed>|string|null
     */
    private function extractBodyForLog(array $options): array|string|null
    {
        if (array_key_exists('json', $options)) {
            return $options['json'];
        }

        if (array_key_exists('query', $options)) {
            return $options['query'];
        }

        return null;
    }

    /**
     * @return array<string, mixed>|string|null
     */
    private function extractResponseBodyForLog(Response $response): array|string|null
    {
        $json = $response->json();
        if (is_array($json)) {
            return $json;
        }

        $max = (int) config('core.http.log.max_body', 2000);
        $body = (string) $response->body();
        return $this->truncateString($body, $max);
    }

    /**
     * @param array<string, mixed>|string|null $body
     * @return array<string, mixed>|string|null
     */
    private function sanitizeBody(array|string|null $body): array|string|null
    {
        if ($body === null) {
            return null;
        }

        if (is_string($body)) {
            $max = (int) config('core.http.log.max_body', 2000);
            return $this->truncateString($body, $max);
        }

        $maskedKeys = (array) config('core.http.log.masked_keys', []);
        $masked = $this->maskArray($body, $maskedKeys);

        return $masked;
    }

    /**
     * @param array<string, mixed> $data
     * @param list<string> $maskedKeys
     * @return array<string, mixed>
     */
    private function maskArray(array $data, array $maskedKeys): array
    {
        $normalized = array_map(static fn ($k) => strtolower((string) $k), $maskedKeys);

        $out = [];
        foreach ($data as $key => $value) {
            $keyStr = (string) $key;
            $keyLower = strtolower($keyStr);

            if (in_array($keyLower, $normalized, true)) {
                $out[$keyStr] = '***';
                continue;
            }

            if (is_array($value)) {
                /** @var array<string, mixed> $value */
                $out[$keyStr] = $this->maskArray($value, $maskedKeys);
                continue;
            }

            $out[$keyStr] = $value;
        }

        return $out;
    }

    private function truncateString(string $value, int $max): string
    {
        if ($max <= 0 || strlen($value) <= $max) {
            return $value;
        }

        return substr($value, 0, $max).'...';
    }
}

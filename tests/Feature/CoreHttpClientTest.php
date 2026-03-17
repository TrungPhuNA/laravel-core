<?php

namespace Tests\Feature;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Infrastructure\Http\CoreHttpClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class CoreHttpClientTest extends TestCase
{
    public function test_it_sends_trace_id_and_locale_headers(): void
    {
        config(['core.http.log.enabled' => false]);

        app()->setLocale('vi');
        app()->instance('request', Request::create('/api/v1/test', 'GET', server: [
            'HTTP_X_REQUEST_ID' => 'trace-123',
        ]));

        Http::fake([
            'catalog.internal/*' => Http::response(['ok' => true], 200),
        ]);

        $client = new CoreHttpClient(serviceName: 'catalog-service', baseUrl: 'http://catalog.internal');
        $client->requestJson('GET', '/v1/health');

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Request-Id', 'trace-123')
                && $request->hasHeader('X-Locale', 'vi')
                && $request->hasHeader('Accept', 'application/json');
        });
    }

    public function test_it_maps_non_2xx_to_api_exception(): void
    {
        config(['core.http.log.enabled' => false]);

        Http::fake([
            'users.internal/*' => Http::response(['error' => 'nope'], 500),
        ]);

        $client = new CoreHttpClient(serviceName: 'users-service', baseUrl: 'http://users.internal');

        try {
            $client->request('GET', '/v1/users/1');
            $this->fail('Expected ApiException');
        } catch (ApiException $e) {
            $this->assertSame(502, $e->status);
            $this->assertSame(ErrorCode::MICROSERVICE_ERROR->value, $e->errorCode);
        }
    }

    public function test_request_json_requires_array_json_body(): void
    {
        config(['core.http.log.enabled' => false]);

        Http::fake([
            'foo.internal/*' => Http::response('"ok"', 200, ['Content-Type' => 'application/json']),
        ]);

        $client = new CoreHttpClient(serviceName: 'foo-service', baseUrl: 'http://foo.internal');

        try {
            $client->requestJson('GET', '/v1/ok');
            $this->fail('Expected ApiException');
        } catch (ApiException $e) {
            $this->assertSame(502, $e->status);
            $this->assertSame(ErrorCode::MICROSERVICE_BAD_RESPONSE->value, $e->errorCode);
        }
    }

    public function test_it_maps_connection_error_to_unavailable(): void
    {
        config(['core.http.log.enabled' => false]);

        Http::fake(function () {
            throw new ConnectionException('timeout');
        });

        $client = new CoreHttpClient(serviceName: 'foo-service', baseUrl: 'http://foo.internal');

        try {
            $client->request('GET', '/v1/ok');
            $this->fail('Expected ApiException');
        } catch (ApiException $e) {
            $this->assertSame(502, $e->status);
            $this->assertSame(ErrorCode::MICROSERVICE_UNAVAILABLE->value, $e->errorCode);
        }
    }
}


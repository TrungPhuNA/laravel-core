<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ApiResponseDurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_success_response_contains_duration_ms(): void
    {
        $res = $this->get('/api/v1/webhooks/health');

        $res->assertOk();
        $res->assertHeader('X-Response-Time-Ms');
        $res->assertJsonPath('meta.duration_ms', $res->json('meta.duration_ms'));
        $this->assertIsInt($res->json('meta.duration_ms'));
        $this->assertGreaterThanOrEqual(0, $res->json('meta.duration_ms'));
    }

    public function test_api_error_response_contains_duration_ms(): void
    {
        $res = $this->get('/api/v1/this-route-does-not-exist');

        $res->assertNotFound();
        $res->assertHeader('X-Response-Time-Ms');
        $this->assertIsInt($res->json('meta.duration_ms'));
        $this->assertGreaterThanOrEqual(0, $res->json('meta.duration_ms'));
    }
}


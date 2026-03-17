<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ApiErrorFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_json_on_method_not_allowed_even_without_accept_json(): void
    {
        // Using "post" (not postJson) to simulate browser-like requests (Accept: text/html).
        $res = $this->post('/api/v1/settings/public');

        $res->assertStatus(400);
        $res->assertHeader('content-type', 'application/json');
        $res->assertJsonPath('status', 'fail');
        $res->assertJsonPath('code', 'METHOD_NOT_ALLOWED');
    }
}


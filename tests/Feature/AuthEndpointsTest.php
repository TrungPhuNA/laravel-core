<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_login_me_profile(): void
    {
        $register = $this->postJson('/api/v1/auth/register', [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'tests',
            'user_type' => 'ADMIN',
        ]);

        $register->assertOk();
        $register->assertJsonPath('status', 'success');
        $register->assertJsonPath('code', 'AUTH_REGISTER_SUCCESS');
        $register->assertJsonPath('data.user.email', 'demo@example.com');
        $register->assertJsonPath('data.user.user_type', 'USER');

        $token = (string) $register->json('data.token');
        $this->assertNotEmpty($token);

        $me = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');
        $me->assertOk();
        $me->assertJsonPath('status', 'success');
        $me->assertJsonPath('data.user.email', 'demo@example.com');

        $profile = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/auth/profile', [
                'phone' => '0900000000',
                'province' => 'HCM',
            ]);
        $profile->assertOk();
        $profile->assertJsonPath('status', 'success');
        $profile->assertJsonPath('data.user.phone', '0900000000');
        $profile->assertJsonPath('data.user.province', 'HCM');

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'demo@example.com',
            'password' => 'password123',
            'device_name' => 'tests',
        ]);
        $login->assertOk();
        $login->assertJsonPath('status', 'success');
        $this->assertNotEmpty((string) $login->json('data.token'));
    }

    public function test_validation_messages_can_switch_vi_en(): void
    {
        $vi = $this->withHeader('Accept-Language', 'vi')
            ->postJson('/api/v1/auth/register', []);

        $vi->assertStatus(400);
        $vi->assertJsonPath('status', 'fail');
        $vi->assertJsonPath('code', 'VALIDATION_ERROR');
        $this->assertStringContainsString('bắt buộc', (string) $vi->json('data.name.0'));

        $en = $this->withHeader('Accept-Language', 'en')
            ->postJson('/api/v1/auth/register', []);

        $en->assertStatus(400);
        $en->assertJsonPath('status', 'fail');
        $en->assertJsonPath('code', 'VALIDATION_ERROR');
        $this->assertStringContainsString('required', (string) $en->json('data.name.0'));
    }
}

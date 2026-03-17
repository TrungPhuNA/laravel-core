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

        $register->assertCreated();
        $register->assertJsonPath('success', true);
        $register->assertJsonPath('data.user.email', 'demo@example.com');
        $register->assertJsonPath('data.user.user_type', 'USER');

        $token = (string) $register->json('data.token');
        $this->assertNotEmpty($token);

        $me = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');
        $me->assertOk();
        $me->assertJsonPath('data.user.email', 'demo@example.com');

        $profile = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/auth/profile', [
                'phone' => '0900000000',
                'province' => 'HCM',
            ]);
        $profile->assertOk();
        $profile->assertJsonPath('data.user.phone', '0900000000');
        $profile->assertJsonPath('data.user.province', 'HCM');

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'demo@example.com',
            'password' => 'password123',
            'device_name' => 'tests',
        ]);
        $login->assertOk();
        $this->assertNotEmpty((string) $login->json('data.token'));
    }
}


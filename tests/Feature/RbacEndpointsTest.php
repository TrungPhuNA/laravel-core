<?php

namespace Tests\Feature;

use App\Core\Support\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class RbacEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_super_admin_admin_is_forbidden_without_permissions(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserType::ADMIN,
            'email' => 'admin-non-super@example.com',
        ]);

        Sanctum::actingAs($admin);

        $res = $this->getJson('/api/v1/users');
        $res->assertStatus(403);
        $res->assertJsonPath('status', 'fail');
        $res->assertJsonPath('code', 'FORBIDDEN');
    }

    public function test_super_admin_email_bypasses_permission_checks(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserType::ADMIN,
            'email' => 'codethue94@gmail.com',
        ]);

        Sanctum::actingAs($admin);

        $res = $this->getJson('/api/v1/users');
        $res->assertOk();
        $res->assertJsonPath('status', 'success');
        $res->assertJsonPath('code', 'USER_LIST_SUCCESS');
    }
}


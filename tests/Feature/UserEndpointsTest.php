<?php

namespace Tests\Feature;

use App\Core\Support\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class UserEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_users_module(): void
    {
        $user = User::factory()->create([
            'user_type' => UserType::USER,
        ]);

        Sanctum::actingAs($user);

        $res = $this->getJson('/api/v1/users');
        $res->assertStatus(403);
        $res->assertJsonPath('status', 'fail');
        $res->assertJsonPath('code', 'FORBIDDEN');
    }

    public function test_admin_can_crud_user_and_restore(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserType::ADMIN,
        ]);

        Sanctum::actingAs($admin);

        $create = $this->postJson('/api/v1/users', [
            'name' => 'Demo',
            'email' => 'demo-user-module@example.com',
            'password' => 'password123',
            'user_type' => 'USER',
        ]);

        $create->assertStatus(201);
        $create->assertJsonPath('status', 'success');
        $id = (int) $create->json('data.user.id');
        $this->assertGreaterThan(0, $id);

        $list = $this->getJson('/api/v1/users?filter[email]=demo-user-module&sort=-id&page=1&per_page=10');
        $list->assertOk();
        $list->assertJsonPath('status', 'success');
        $list->assertJsonPath('code', 'USER_LIST_SUCCESS');
        $this->assertNotNull($list->json('meta.pagination.total'));

        $type = $this->patchJson("/api/v1/users/{$id}/user-type", [
            'user_type' => 'ADMIN',
        ]);
        $type->assertOk();
        $type->assertJsonPath('data.user.user_type', 'ADMIN');

        $delete = $this->deleteJson("/api/v1/users/{$id}");
        $delete->assertOk();
        $delete->assertJsonPath('code', 'USER_DELETE_SUCCESS');

        $restore = $this->postJson("/api/v1/users/{$id}/restore");
        $restore->assertOk();
        $restore->assertJsonPath('code', 'USER_RESTORE_SUCCESS');
        $restore->assertJsonPath('data.user.deleted_at', null);
    }
}


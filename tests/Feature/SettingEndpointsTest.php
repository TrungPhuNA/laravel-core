<?php

namespace Tests\Feature;

use App\Core\Support\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Setting\Domain\Models\Setting;
use Tests\TestCase;

final class SettingEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_settings_list_returns_only_public_items(): void
    {
        Setting::create(['key' => 'site_name', 'value' => 'Core API', 'group' => 'general', 'is_public' => true]);
        Setting::create(['key' => 'secret_key', 'value' => 'xxx', 'group' => 'security', 'is_public' => false]);

        $res = $this->getJson('/api/v1/settings/public');
        $res->assertOk();
        $res->assertJsonPath('status', 'success');
        $this->assertCount(1, (array) $res->json('data.items'));
        $res->assertJsonPath('data.items.0.key', 'site_name');
    }

    public function test_admin_can_upsert_and_list_settings(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::ADMIN]);
        Sanctum::actingAs($admin);

        $upsert = $this->putJson('/api/v1/settings', [
            'items' => [
                [
                    'key' => 'site_name',
                    'value' => 'Core API',
                    'group' => 'general',
                    'is_public' => true,
                    'description' => 'Tên website',
                ],
            ],
        ]);

        $upsert->assertOk();
        $upsert->assertJsonPath('status', 'success');

        $list = $this->getJson('/api/v1/settings');
        $list->assertOk();
        $list->assertJsonPath('status', 'success');
        $list->assertJsonPath('data.items.0.key', 'site_name');
    }

    public function test_user_type_user_is_forbidden(): void
    {
        $user = User::factory()->create(['user_type' => UserType::USER]);
        Sanctum::actingAs($user);

        $res = $this->putJson('/api/v1/settings', [
            'items' => [
                ['key' => 'site_name', 'value' => 'Core API'],
            ],
        ]);

        $res->assertStatus(403);
        $res->assertJsonPath('status', 'fail');
        $res->assertJsonPath('code', 'FORBIDDEN');
    }
}


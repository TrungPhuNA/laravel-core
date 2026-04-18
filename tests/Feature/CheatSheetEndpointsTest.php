<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class CheatSheetEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_crud_cheat_sheet_with_tags(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $create = $this->postJson('/api/v1/cheat-sheets', [
            'title' => 'Laravel notes',
            'body' => 'Cache::remember',
            'tags' => ['php', 'laravel'],
        ]);

        $create->assertStatus(201);
        $create->assertJsonPath('status', 'success');
        $create->assertJsonPath('code', 'CHEATSHEET_CREATE_SUCCESS');

        $id = (int) $create->json('data.cheat_sheet.id');
        $this->assertGreaterThan(0, $id);

        $list = $this->getJson('/api/v1/cheat-sheets?filters[q]=Cache&per_page=10');
        $list->assertOk();
        $list->assertJsonPath('status', 'success');
        $list->assertJsonPath('code', 'CHEATSHEET_LIST_SUCCESS');
        $this->assertNotNull($list->json('meta.pagination.total'));

        $filterByTag = $this->getJson('/api/v1/cheat-sheets?filters[tag]=laravel');
        $filterByTag->assertOk();
        $filterByTag->assertJsonPath('status', 'success');

        $show = $this->getJson("/api/v1/cheat-sheets/{$id}");
        $show->assertOk();
        $show->assertJsonPath('code', 'CHEATSHEET_SHOW_SUCCESS');
        $show->assertJsonPath('data.cheat_sheet.title', 'Laravel notes');

        $update = $this->putJson("/api/v1/cheat-sheets/{$id}", [
            'title' => 'Laravel notes (updated)',
            'tags' => ['sanctum', 'auth'],
        ]);
        $update->assertOk();
        $update->assertJsonPath('code', 'CHEATSHEET_UPDATE_SUCCESS');
        $update->assertJsonPath('data.cheat_sheet.title', 'Laravel notes (updated)');

        $tags = $this->getJson('/api/v1/cheat-sheets/tags?q=auth&limit=10');
        $tags->assertOk();
        $tags->assertJsonPath('code', 'CHEATSHEET_TAGS_SUCCESS');

        $topics = $this->getJson('/api/v1/cheat-sheets/topics?limit=50');
        $topics->assertOk();
        $topics->assertJsonPath('code', 'CHEATSHEET_TOPICS_SUCCESS');

        $delete = $this->deleteJson("/api/v1/cheat-sheets/{$id}");
        $delete->assertOk();
        $delete->assertJsonPath('code', 'CHEATSHEET_DELETE_SUCCESS');
    }

    public function test_user_cannot_access_other_users_cheat_sheet(): void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);

        $create = $this->postJson('/api/v1/cheat-sheets', [
            'title' => 'Private sheet',
            'body' => 'secret',
        ]);
        $create->assertStatus(201);
        $id = (int) $create->json('data.cheat_sheet.id');

        $other = User::factory()->create();
        Sanctum::actingAs($other);

        $show = $this->getJson("/api/v1/cheat-sheets/{$id}");
        $show->assertStatus(404);
    }
}

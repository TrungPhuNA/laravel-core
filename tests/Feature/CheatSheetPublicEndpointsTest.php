<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CheatSheet\Domain\Models\CheatSheet;
use Modules\CheatSheet\Domain\Models\CheatSheetTag;
use Tests\TestCase;

final class CheatSheetPublicEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_topics_list_and_show(): void
    {
        $user = User::factory()->create();

        /** @var CheatSheet $sheet */
        $sheet = CheatSheet::query()->create([
            'user_id' => $user->id,
            'title' => 'Public Laravel Cache',
            'body' => "# Cache\n\n```php\nCache::remember('k', 60, fn () => 1);\n```",
            'visibility' => 'public',
            'published_at' => now(),
        ]);

        /** @var CheatSheetTag $tag */
        $tag = CheatSheetTag::query()->create([
            'user_id' => $user->id,
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $sheet->tags()->sync([$tag->id]);

        $topics = $this->getJson('/api/v1/public/cheat-sheets/topics?limit=50');
        $topics->assertOk();
        $topics->assertJsonPath('status', 'success');
        $topics->assertJsonPath('code', 'CHEATSHEET_PUBLIC_TOPICS_SUCCESS');

        $list = $this->getJson('/api/v1/public/cheat-sheets?filters[tag]=laravel&page=1&per_page=10');
        $list->assertOk();
        $list->assertJsonPath('code', 'CHEATSHEET_PUBLIC_LIST_SUCCESS');
        $this->assertNotNull($list->json('meta.pagination.total'));

        $show = $this->getJson("/api/v1/public/cheat-sheets/{$sheet->id}");
        $show->assertOk();
        $show->assertJsonPath('code', 'CHEATSHEET_PUBLIC_SHOW_SUCCESS');
        $show->assertJsonPath('data.cheat_sheet.title', 'Public Laravel Cache');
    }
}


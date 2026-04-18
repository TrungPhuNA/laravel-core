<?php

namespace Modules\CheatSheet\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\CheatSheet\Domain\Models\CheatSheet;
use Modules\CheatSheet\Domain\Models\CheatSheetTag;

final class CheatSheetDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $user = User::query()->firstOrCreate(
                ['email' => 'cheatsheet-demo@example.com'],
                [
                    'name' => 'CheatSheet Demo',
                    'password' => Hash::make('password123'),
                    'user_type' => 'USER',
                ],
            );

            $rows = [
                [
                    'title' => 'Laravel: Cache',
                    'visibility' => 'private',
                    'tags' => ['laravel', 'cache'],
                    'body' => implode("\n", [
                        "# Cache quick notes",
                        "",
                        "## Remember",
                        "```php",
                        "Cache::remember('key', 60, fn () => 'value');",
                        "```",
                        "",
                        "## Forget",
                        "```php",
                        "Cache::forget('key');",
                        "```",
                    ]),
                ],
                [
                    'title' => 'Laravel: Sanctum token',
                    'visibility' => 'unlisted',
                    'tags' => ['laravel', 'sanctum', 'auth'],
                    'body' => implode("\n", [
                        "# Sanctum token",
                        "",
                        "- Dùng Bearer token cho API-first.",
                        "- Token lưu ở `localStorage` key `core_api_token` để admin UI gọi API.",
                        "",
                        "```bash",
                        "curl -H \"Authorization: Bearer <token>\" http://laravel-core.test/api/v1/auth/me",
                        "```",
                    ]),
                ],
                [
                    'title' => 'SQL: Useful patterns',
                    'visibility' => 'public',
                    'tags' => ['sql', 'tips'],
                    'body' => implode("\n", [
                        "# SQL patterns",
                        "",
                        "## Count + group",
                        "```sql",
                        "select status, count(*) from orders group by status;",
                        "```",
                        "",
                        "## Like search",
                        "```sql",
                        "select * from users where email like '%@gmail.com%';",
                        "```",
                    ]),
                ],
                [
                    'title' => 'Git: everyday commands',
                    'visibility' => 'private',
                    'tags' => ['git', 'dev'],
                    'body' => implode("\n", [
                        "# Git everyday",
                        "",
                        "- `git status`",
                        "- `git add -A`",
                        "- `git commit -m \"...\"`",
                        "- `git pull --rebase`",
                    ]),
                ],
            ];

            foreach ($rows as $row) {
                /** @var CheatSheet $sheet */
                $sheet = CheatSheet::query()->create([
                    'user_id' => $user->id,
                    'title' => $row['title'],
                    'body' => $row['body'],
                    'visibility' => $row['visibility'],
                    'published_at' => $row['visibility'] === 'public' ? now() : null,
                ]);

                $tagIds = [];
                foreach ($row['tags'] as $name) {
                    $slug = Str::slug($name);
                    /** @var CheatSheetTag $tag */
                    $tag = CheatSheetTag::query()->firstOrCreate(
                        ['user_id' => $user->id, 'slug' => $slug],
                        ['name' => $name],
                    );
                    $tagIds[] = $tag->id;
                }

                $sheet->tags()->sync($tagIds);
            }
        });
    }
}


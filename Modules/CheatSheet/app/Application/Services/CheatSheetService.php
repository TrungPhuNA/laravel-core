<?php

namespace Modules\CheatSheet\Application\Services;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Support\Query\ApiQueryParams;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\CheatSheet\Application\Contracts\CheatSheetServiceInterface;
use Modules\CheatSheet\Infrastructure\Contracts\CheatSheetRepositoryInterface;
use Modules\CheatSheet\Domain\Models\CheatSheet;
use Modules\CheatSheet\Domain\Models\CheatSheetTag;

final class CheatSheetService implements CheatSheetServiceInterface
{
    /**
     * @var list<string>
     */
    private const VISIBILITIES = ['private', 'unlisted', 'public'];

    public function __construct(
        private readonly CheatSheetRepositoryInterface $repo,
    ) {}

    public function paginateForUser(User $user, ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->repo->paginateForUser($user, $params);
    }

    public function getForUserById(User $user, int $id): CheatSheet
    {
        return $this->repo->findForUserOrFail($user, $id);
    }

    public function createForUser(User $user, array $input): CheatSheet
    {
        $title = trim((string) ($input['title'] ?? ''));
        $body = (string) ($input['body'] ?? '');
        $visibility = $this->normalizeVisibility($input['visibility'] ?? 'private');

        if ($title === '') {
            throw ApiException::unprocessable(
                ErrorCode::VALIDATION_ERROR->value,
                __('messages.validation_error'),
                ['title' => ['The title field is required.']],
            );
        }

        return DB::transaction(function () use ($user, $input, $title, $body, $visibility) {
            $sheet = $this->repo->create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'visibility' => $visibility,
                'published_at' => $input['published_at'] ?? null,
            ]);

            $tags = $this->normalizeTags($input['tags'] ?? []);
            if ($tags !== []) {
                $this->syncTags($user, $sheet, $tags);
            }

            return $sheet->load('tags');
        });
    }

    public function updateForUser(User $user, int $id, array $input): CheatSheet
    {
        $sheet = $this->repo->findForUserOrFail($user, $id);

        $payload = [];

        if (array_key_exists('title', $input)) {
            $title = trim((string) $input['title']);
            if ($title === '') {
                throw ApiException::unprocessable(
                    ErrorCode::VALIDATION_ERROR->value,
                    __('messages.validation_error'),
                    ['title' => ['The title field is required.']],
                );
            }
            $payload['title'] = $title;
        }

        if (array_key_exists('body', $input)) {
            $payload['body'] = (string) $input['body'];
        }

        if (array_key_exists('visibility', $input)) {
            $payload['visibility'] = $this->normalizeVisibility($input['visibility']);
        }

        if (array_key_exists('published_at', $input)) {
            $payload['published_at'] = $input['published_at'];
        }

        return DB::transaction(function () use ($user, $sheet, $payload, $input) {
            $sheet = $this->repo->update($sheet, $payload);

            if (array_key_exists('tags', $input)) {
                $tags = $this->normalizeTags($input['tags'] ?? []);
                $this->syncTags($user, $sheet, $tags);
            }

            return $sheet->load('tags');
        });
    }

    public function deleteForUser(User $user, int $id): void
    {
        $sheet = $this->repo->findForUserOrFail($user, $id);

        DB::transaction(function () use ($sheet) {
            $this->repo->delete($sheet);
        });
    }

    public function listTagsForUser(User $user, ?string $q = null, int $limit = 20): Collection
    {
        $limit = max(1, min($limit, 50));
        $query = CheatSheetTag::query()
            ->where('user_id', $user->id)
            ->orderBy('name')
            ->limit($limit);

        if (is_string($q)) {
            $q = trim($q);
            if ($q !== '') {
                $query->where('name', 'like', '%'.$q.'%');
            }
        }

        return $query->get();
    }

    public function listTopicsForUser(User $user, ?string $q = null, int $limit = 50): Collection
    {
        $limit = max(1, min($limit, 100));

        $query = CheatSheetTag::query()
            ->select([
                'cheat_sheet_tags.*',
                DB::raw('COUNT(DISTINCT cheat_sheet_tag.cheat_sheet_id) as cheat_sheets_count'),
            ])
            ->leftJoin('cheat_sheet_tag', 'cheat_sheet_tag.tag_id', '=', 'cheat_sheet_tags.id')
            ->leftJoin('cheat_sheets', function ($join) use ($user) {
                $join->on('cheat_sheets.id', '=', 'cheat_sheet_tag.cheat_sheet_id')
                    ->whereNull('cheat_sheets.deleted_at')
                    ->where('cheat_sheets.user_id', '=', $user->id);
            })
            ->where('cheat_sheet_tags.user_id', $user->id)
            ->groupBy('cheat_sheet_tags.id')
            ->orderByDesc('cheat_sheets_count')
            ->orderBy('cheat_sheet_tags.name')
            ->limit($limit);

        if (is_string($q)) {
            $q = trim($q);
            if ($q !== '') {
                $query->where('cheat_sheet_tags.name', 'like', '%'.$q.'%');
            }
        }

        return $query->get();
    }

    /**
     * @param mixed $value
     */
    private function normalizeVisibility(mixed $value): string
    {
        $v = strtolower(trim((string) $value));

        if (!in_array($v, self::VISIBILITIES, true)) {
            throw ApiException::unprocessable(
                ErrorCode::VALIDATION_ERROR->value,
                __('messages.validation_error'),
                ['visibility' => ['Invalid visibility.']],
            );
        }

        return $v;
    }

    /**
     * @param mixed $value
     * @return list<string>
     */
    private function normalizeTags(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $tags = array_values(array_filter(array_map(static fn ($v) => is_string($v) ? trim($v) : '', $value), static fn ($v) => $v !== ''));
        $tags = array_values(array_unique($tags));

        return array_slice($tags, 0, 50);
    }

    /**
     * @param list<string> $tags
     */
    private function syncTags(User $user, CheatSheet $sheet, array $tags): void
    {
        $tagIds = [];

        foreach ($tags as $name) {
            $slug = $this->tagSlug($name);
            if ($slug === '') {
                continue;
            }

            /** @var CheatSheetTag $tag */
            $tag = CheatSheetTag::query()->firstOrCreate(
                ['user_id' => $user->id, 'slug' => $slug],
                ['name' => $name],
            );

            $tagIds[] = $tag->id;
        }

        $sheet->tags()->sync($tagIds);
    }

    private function tagSlug(string $name): string
    {
        $slug = Str::slug($name);

        if ($slug !== '') {
            return $slug;
        }

        // Fallback cho tag chỉ có ký tự đặc biệt (ví dụ: "C++", "C#").
        $fallback = strtolower(trim($name));
        $fallback = preg_replace('/[^a-z0-9]+/i', '-', $fallback) ?? '';
        $fallback = trim($fallback, '-');

        return $fallback;
    }
}

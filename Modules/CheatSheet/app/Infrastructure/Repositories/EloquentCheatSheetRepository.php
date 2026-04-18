<?php

namespace Modules\CheatSheet\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CheatSheet\Infrastructure\Contracts\CheatSheetRepositoryInterface;
use Modules\CheatSheet\Infrastructure\Query\CheatSheetQueryConfig;
use Modules\CheatSheet\Domain\Models\CheatSheet;

final class EloquentCheatSheetRepository implements CheatSheetRepositoryInterface
{
    public function paginateForUser(User $user, ApiQueryParams $params): LengthAwarePaginator
    {
        $query = CheatSheet::query()
            ->where('user_id', $user->id)
            ->with('tags');

        // Custom filters: q, tag(s)
        $this->applyCustomFilters($query, $params);

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: CheatSheetQueryConfig::allowedFilters(),
            allowedSorts: CheatSheetQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: CheatSheetQueryConfig::defaultSorts(),
        );

        return $query->paginate(
            perPage: $params->perPage,
            page: $params->page,
        );
    }

    public function paginatePublic(ApiQueryParams $params): LengthAwarePaginator
    {
        $query = CheatSheet::query()
            ->where('visibility', 'public')
            ->with('tags');

        $this->applyCustomFilters($query, $params);

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: CheatSheetQueryConfig::allowedFilters(),
            allowedSorts: CheatSheetQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: ['-published_at', '-updated_at', '-id'],
        );

        return $query->paginate(
            perPage: $params->perPage,
            page: $params->page,
        );
    }

    public function findForUserOrFail(User $user, int $id): CheatSheet
    {
        /** @var CheatSheet $sheet */
        $sheet = CheatSheet::query()
            ->where('user_id', $user->id)
            ->with('tags')
            ->findOrFail($id);

        return $sheet;
    }

    public function findPublicOrFail(int $id): CheatSheet
    {
        /** @var CheatSheet $sheet */
        $sheet = CheatSheet::query()
            ->where('visibility', 'public')
            ->with('tags')
            ->findOrFail($id);

        return $sheet;
    }

    public function create(array $input): CheatSheet
    {
        /** @var CheatSheet $sheet */
        $sheet = CheatSheet::query()->create($input);

        return $sheet->refresh();
    }

    public function update(CheatSheet $sheet, array $input): CheatSheet
    {
        $sheet->fill($input);
        $sheet->save();

        return $sheet->refresh();
    }

    public function delete(CheatSheet $sheet): void
    {
        $sheet->delete();
    }

    private function applyCustomFilters(\Illuminate\Database\Eloquent\Builder $query, ApiQueryParams $params): void
    {
        $filters = $params->filters;

        // filters[q]=... => search title/body
        $q = $filters['q'] ?? null;
        if (is_string($q)) {
            $q = trim($q);
            if ($q !== '') {
                $query->where(static function ($w) use ($q) {
                    $w->where('title', 'like', '%'.$q.'%')
                        ->orWhere('body', 'like', '%'.$q.'%');
                });
            }
        }

        // filters[tag]=laravel hoặc filters[tag]=laravel,php
        $tag = $filters['tag'] ?? $filters['tags'] ?? null;
        if (is_string($tag)) {
            $tag = trim($tag);
            if ($tag !== '') {
                $tags = array_values(array_filter(array_map('trim', explode(',', $tag)), static fn ($v) => $v !== ''));
                if ($tags !== []) {
                    $slugs = array_map(static fn ($v) => \Illuminate\Support\Str::slug($v), $tags);

                    $query->whereHas('tags', static function ($tq) use ($slugs) {
                        $tq->whereIn('slug', $slugs);
                    });
                }
            }
        }
    }
}

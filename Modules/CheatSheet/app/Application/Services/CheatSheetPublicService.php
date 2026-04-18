<?php

namespace Modules\CheatSheet\Application\Services;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\CheatSheet\Application\Contracts\CheatSheetPublicServiceInterface;
use Modules\CheatSheet\Domain\Models\CheatSheet;
use Modules\CheatSheet\Infrastructure\Contracts\CheatSheetRepositoryInterface;

final class CheatSheetPublicService implements CheatSheetPublicServiceInterface
{
    public function __construct(
        private readonly CheatSheetRepositoryInterface $repo,
    ) {}

    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->repo->paginatePublic($params);
    }

    public function getById(int $id): CheatSheet
    {
        return $this->repo->findPublicOrFail($id);
    }

    public function listTopics(?string $q = null, int $limit = 60): Collection
    {
        $limit = max(1, min($limit, 200));

        $query = DB::table('cheat_sheet_tags as t')
            ->selectRaw('t.slug as slug, MIN(t.name) as name, COUNT(DISTINCT pivot.cheat_sheet_id) as count')
            ->join('cheat_sheet_tag as pivot', 'pivot.tag_id', '=', 't.id')
            ->join('cheat_sheets as s', 's.id', '=', 'pivot.cheat_sheet_id')
            ->whereNull('s.deleted_at')
            ->where('s.visibility', '=', 'public')
            ->groupBy('t.slug')
            ->orderByDesc('count')
            ->orderBy('name')
            ->limit($limit);

        if (is_string($q)) {
            $q = trim($q);
            if ($q !== '') {
                $query->where('t.name', 'like', '%'.$q.'%');
            }
        }

        /** @var Collection<int, object{slug:string,name:string,count:int}> $rows */
        $rows = $query->get();

        return $rows->map(static function ($r) {
            return [
                'slug' => (string) $r->slug,
                'name' => (string) $r->name,
                'count' => (int) $r->count,
            ];
        });
    }
}


<?php

namespace Modules\CheatSheet\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\CheatSheet\Domain\Models\CheatSheet;

interface CheatSheetPublicServiceInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function getById(int $id): CheatSheet;

    /**
     * @return Collection<int, array{slug: string, name: string, count: int}>
     */
    public function listTopics(?string $q = null, int $limit = 60): Collection;
}


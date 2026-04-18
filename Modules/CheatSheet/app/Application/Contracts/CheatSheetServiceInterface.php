<?php

namespace Modules\CheatSheet\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\CheatSheet\Domain\Models\CheatSheet;

interface CheatSheetServiceInterface
{
    public function paginateForUser(User $user, ApiQueryParams $params): LengthAwarePaginator;

    public function getForUserById(User $user, int $id): CheatSheet;

    public function createForUser(User $user, array $input): CheatSheet;

    public function updateForUser(User $user, int $id, array $input): CheatSheet;

    public function deleteForUser(User $user, int $id): void;

    /**
     * @return Collection<int, \Modules\CheatSheet\Domain\Models\CheatSheetTag>
     */
    public function listTagsForUser(User $user, ?string $q = null, int $limit = 20): Collection;

    /**
     * @return Collection<int, \Modules\CheatSheet\Domain\Models\CheatSheetTag>
     */
    public function listTopicsForUser(User $user, ?string $q = null, int $limit = 50): Collection;
}

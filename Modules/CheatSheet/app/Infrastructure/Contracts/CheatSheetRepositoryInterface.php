<?php

namespace Modules\CheatSheet\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CheatSheet\Domain\Models\CheatSheet;

interface CheatSheetRepositoryInterface
{
    public function paginateForUser(User $user, ApiQueryParams $params): LengthAwarePaginator;

    public function paginatePublic(ApiQueryParams $params): LengthAwarePaginator;

    public function findForUserOrFail(User $user, int $id): CheatSheet;

    public function findPublicOrFail(int $id): CheatSheet;

    public function create(array $input): CheatSheet;

    public function update(CheatSheet $sheet, array $input): CheatSheet;

    public function delete(CheatSheet $sheet): void;
}

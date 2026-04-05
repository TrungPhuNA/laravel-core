<?php

namespace Modules\Ecommerce\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Category;

interface CategoryServiceInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function getById(int $id): Category;

    public function create(array $input): Category;

    public function update(int $id, array $input): Category;

    public function delete(int $id): void;
}


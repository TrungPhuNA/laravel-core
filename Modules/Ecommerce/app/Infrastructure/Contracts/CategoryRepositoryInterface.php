<?php

namespace Modules\Ecommerce\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Category;

interface CategoryRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function findOrFail(int $id): Category;

    public function existsBySlug(string $slug, ?int $exceptId = null): bool;

    public function create(array $input): Category;

    public function update(Category $category, array $input): Category;

    public function delete(Category $category): void;
}


<?php

namespace Modules\Ecommerce\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Product;

interface ProductRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function findOrFail(int $id): Product;

    public function existsBySku(string $sku, ?int $exceptId = null): bool;

    public function existsBySlug(string $slug, ?int $exceptId = null): bool;

    public function create(array $input): Product;

    public function update(Product $product, array $input): Product;

    public function delete(Product $product): void;
}


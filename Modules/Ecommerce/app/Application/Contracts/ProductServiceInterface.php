<?php

namespace Modules\Ecommerce\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Product;

interface ProductServiceInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function getById(int $id): Product;

    public function create(array $input): Product;

    public function update(int $id, array $input): Product;

    public function delete(int $id): void;
}


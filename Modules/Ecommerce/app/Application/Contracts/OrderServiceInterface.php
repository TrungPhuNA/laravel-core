<?php

namespace Modules\Ecommerce\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Order;

interface OrderServiceInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function getById(int $id): Order;

    public function create(array $input): Order;

    public function update(int $id, array $input): Order;

    public function delete(int $id): void;
}


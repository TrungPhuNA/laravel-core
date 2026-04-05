<?php

namespace Modules\Ecommerce\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Order;

interface OrderRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function findOrFail(int $id): Order;

    public function create(array $input): Order;

    public function update(Order $order, array $input): Order;

    public function delete(Order $order): void;
}


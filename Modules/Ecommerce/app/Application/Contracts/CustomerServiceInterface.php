<?php

namespace Modules\Ecommerce\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Customer;

interface CustomerServiceInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function getById(int $id): Customer;

    public function create(array $input): Customer;

    public function update(int $id, array $input): Customer;

    public function delete(int $id): void;
}


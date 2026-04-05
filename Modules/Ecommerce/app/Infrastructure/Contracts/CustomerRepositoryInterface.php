<?php

namespace Modules\Ecommerce\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Customer;

interface CustomerRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function findOrFail(int $id): Customer;

    public function existsByEmail(string $email, ?int $exceptId = null): bool;

    public function create(array $input): Customer;

    public function update(Customer $customer, array $input): Customer;

    public function delete(Customer $customer): void;
}


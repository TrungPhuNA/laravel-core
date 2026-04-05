<?php

namespace Modules\Ecommerce\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Customer;
use Modules\Ecommerce\Infrastructure\Contracts\CustomerRepositoryInterface;
use Modules\Ecommerce\Infrastructure\Query\CustomerQueryConfig;
use Modules\Ecommerce\Support\ShopResolver;

final class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        $query = Customer::query()->where('shop_id', ShopResolver::id());

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: CustomerQueryConfig::allowedFilters(),
            allowedSorts: CustomerQueryConfig::allowedSorts(),
            allowedIncludes: ['addresses', 'orders'],
            defaultSorts: CustomerQueryConfig::defaultSorts(),
        );

        return $query->paginate(
            perPage: $params->perPage,
            page: $params->page,
        );
    }

    public function findOrFail(int $id): Customer
    {
        /** @var Customer $customer */
        $customer = Customer::query()
            ->where('shop_id', ShopResolver::id())
            ->findOrFail($id);

        return $customer;
    }

    public function existsByEmail(string $email, ?int $exceptId = null): bool
    {
        $email = trim($email);
        if ($email === '') {
            return false;
        }

        $q = Customer::query()
            ->where('shop_id', ShopResolver::id())
            ->where('email', $email);
        if ($exceptId !== null) {
            $q->where('id', '!=', $exceptId);
        }

        return $q->exists();
    }

    public function create(array $input): Customer
    {
        /** @var Customer $customer */
        $customer = Customer::query()->create($input);

        return $customer->refresh();
    }

    public function update(Customer $customer, array $input): Customer
    {
        $customer->fill($input);
        $customer->save();

        return $customer->refresh();
    }

    public function delete(Customer $customer): void
    {
        $customer->delete();
    }
}

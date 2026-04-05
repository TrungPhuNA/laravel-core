<?php

namespace Modules\Ecommerce\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Order;
use Modules\Ecommerce\Infrastructure\Contracts\OrderRepositoryInterface;
use Modules\Ecommerce\Infrastructure\Query\OrderQueryConfig;
use Modules\Ecommerce\Support\ShopResolver;

final class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        $query = Order::query()->where('shop_id', ShopResolver::id());

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: OrderQueryConfig::allowedFilters(),
            allowedSorts: OrderQueryConfig::allowedSorts(),
            allowedIncludes: ['items', 'customer'],
            defaultSorts: OrderQueryConfig::defaultSorts(),
        );

        return $query->paginate(
            perPage: $params->perPage,
            page: $params->page,
        );
    }

    public function findOrFail(int $id): Order
    {
        /** @var Order $order */
        $order = Order::query()
            ->where('shop_id', ShopResolver::id())
            ->with(['items', 'customer'])
            ->findOrFail($id);

        return $order;
    }

    public function create(array $input): Order
    {
        /** @var Order $order */
        $order = Order::query()->create($input);

        return $order->refresh();
    }

    public function update(Order $order, array $input): Order
    {
        $order->fill($input);
        $order->save();

        return $order->refresh();
    }

    public function delete(Order $order): void
    {
        $order->delete();
    }
}

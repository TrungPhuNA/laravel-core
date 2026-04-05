<?php

namespace Modules\Ecommerce\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Product;
use Modules\Ecommerce\Infrastructure\Contracts\ProductRepositoryInterface;
use Modules\Ecommerce\Infrastructure\Query\ProductQueryConfig;
use Modules\Ecommerce\Support\ShopResolver;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        $query = Product::query()->where('shop_id', ShopResolver::id());

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: ProductQueryConfig::allowedFilters(),
            allowedSorts: ProductQueryConfig::allowedSorts(),
            allowedIncludes: ['categories'],
            defaultSorts: ProductQueryConfig::defaultSorts(),
        );

        return $query->paginate(
            perPage: $params->perPage,
            page: $params->page,
        );
    }

    public function findOrFail(int $id): Product
    {
        /** @var Product $product */
        $product = Product::query()
            ->where('shop_id', ShopResolver::id())
            ->with(['categories'])
            ->findOrFail($id);

        return $product;
    }

    public function existsBySku(string $sku, ?int $exceptId = null): bool
    {
        $q = Product::query()
            ->where('shop_id', ShopResolver::id())
            ->where('sku', $sku);
        if ($exceptId !== null) {
            $q->where('id', '!=', $exceptId);
        }

        return $q->exists();
    }

    public function existsBySlug(string $slug, ?int $exceptId = null): bool
    {
        $q = Product::query()
            ->where('shop_id', ShopResolver::id())
            ->where('slug', $slug);
        if ($exceptId !== null) {
            $q->where('id', '!=', $exceptId);
        }

        return $q->exists();
    }

    public function create(array $input): Product
    {
        /** @var Product $product */
        $product = Product::query()->create($input);

        return $product->refresh();
    }

    public function update(Product $product, array $input): Product
    {
        $product->fill($input);
        $product->save();

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}

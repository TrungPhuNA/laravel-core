<?php

namespace Modules\Ecommerce\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Ecommerce\Domain\Models\Category;
use Modules\Ecommerce\Infrastructure\Contracts\CategoryRepositoryInterface;
use Modules\Ecommerce\Infrastructure\Query\CategoryQueryConfig;
use Modules\Ecommerce\Support\ShopResolver;

final class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        $query = Category::query()->where('shop_id', ShopResolver::id());

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: CategoryQueryConfig::allowedFilters(),
            allowedSorts: CategoryQueryConfig::allowedSorts(),
            allowedIncludes: ['parent', 'children'],
            defaultSorts: CategoryQueryConfig::defaultSorts(),
        );

        return $query->paginate(
            perPage: $params->perPage,
            page: $params->page,
        );
    }

    public function findOrFail(int $id): Category
    {
        /** @var Category $category */
        $category = Category::query()
            ->where('shop_id', ShopResolver::id())
            ->findOrFail($id);

        return $category;
    }

    public function existsBySlug(string $slug, ?int $exceptId = null): bool
    {
        $q = Category::query()
            ->where('shop_id', ShopResolver::id())
            ->where('slug', $slug);
        if ($exceptId !== null) {
            $q->where('id', '!=', $exceptId);
        }

        return $q->exists();
    }

    public function create(array $input): Category
    {
        /** @var Category $category */
        $category = Category::query()->create($input);

        return $category->refresh();
    }

    public function update(Category $category, array $input): Category
    {
        $category->fill($input);
        $category->save();

        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}

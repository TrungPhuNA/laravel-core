<?php

namespace Modules\Ecommerce\Application\Services;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Ecommerce\Application\Contracts\ProductServiceInterface;
use Modules\Ecommerce\Domain\Models\Product;
use Modules\Ecommerce\Infrastructure\Contracts\ProductRepositoryInterface;
use Modules\Ecommerce\Support\ShopResolver;

final class ProductService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->products->paginate($params);
    }

    public function getById(int $id): Product
    {
        return $this->products->findOrFail($id);
    }

    public function create(array $input): Product
    {
        $shopId = ShopResolver::id();
        $input['shop_id'] = $shopId;
        [$data, $categoryIds] = $this->normalize($input);

        if ($this->products->existsBySku((string) $data['sku'])) {
            throw ApiException::unprocessable(
                ErrorCode::VALIDATION_ERROR->value,
                __('messages.validation_error'),
                ['sku' => ['SKU already exists']],
            );
        }

        if ($this->products->existsBySlug((string) $data['slug'])) {
            throw ApiException::unprocessable(
                ErrorCode::VALIDATION_ERROR->value,
                __('messages.validation_error'),
                ['slug' => ['Slug already exists']],
            );
        }

        return DB::transaction(function () use ($data, $categoryIds, $shopId) {
            $product = $this->products->create($data);
            if ($categoryIds !== null) {
                $product->categories()->sync($this->pivotForShop($categoryIds, $shopId));
            }
            return $product->refresh();
        });
    }

    public function update(int $id, array $input): Product
    {
        $product = $this->products->findOrFail($id);

        $shopId = $product->shop_id;
        [$data, $categoryIds] = $this->normalize($input, partial: true);

        if (array_key_exists('sku', $data) && $data['sku'] !== null) {
            $sku = (string) $data['sku'];
            if ($this->products->existsBySku($sku, exceptId: $product->id)) {
                throw ApiException::unprocessable(
                    ErrorCode::VALIDATION_ERROR->value,
                    __('messages.validation_error'),
                    ['sku' => ['SKU already exists']],
                );
            }
        }

        if (array_key_exists('slug', $data) && $data['slug'] !== null) {
            $slug = (string) $data['slug'];
            if ($this->products->existsBySlug($slug, exceptId: $product->id)) {
                throw ApiException::unprocessable(
                    ErrorCode::VALIDATION_ERROR->value,
                    __('messages.validation_error'),
                    ['slug' => ['Slug already exists']],
                );
            }
        }

        return DB::transaction(function () use ($product, $data, $categoryIds, $shopId) {
            $product = $this->products->update($product, $data);
            if ($categoryIds !== null) {
                $product->categories()->sync($this->pivotForShop($categoryIds, $shopId));
            }
            return $product->refresh();
        });
    }

    public function delete(int $id): void
    {
        $product = $this->products->findOrFail($id);
        DB::transaction(fn () => $this->products->delete($product));
    }

    /**
     * @return array{0: array<string, mixed>, 1: list<int>|null}
     */
    private function normalize(array $input, bool $partial = false): array
    {
        $categoryIds = null;

        if (array_key_exists('category_ids', $input)) {
            $raw = $input['category_ids'];
            if (is_array($raw)) {
                $categoryIds = array_values(array_unique(array_map('intval', $raw)));
            } else {
                $categoryIds = [];
            }
            unset($input['category_ids']);
        }

        if (!$partial || array_key_exists('sku', $input)) {
            $input['sku'] = Str::upper(trim((string) ($input['sku'] ?? '')));
        }

        if (!$partial || array_key_exists('name', $input)) {
            $input['name'] = trim((string) ($input['name'] ?? ''));
        }

        if (!$partial || array_key_exists('slug', $input)) {
            $raw = (string) ($input['slug'] ?? '');
            $raw = trim($raw);
            if ($raw === '' && isset($input['name'])) {
                $raw = (string) $input['name'];
            }
            $input['slug'] = Str::slug($raw);
        }

        if (array_key_exists('description', $input)) {
            $desc = $input['description'];
            $desc = is_string($desc) ? trim($desc) : $desc;
            $input['description'] = $desc === '' ? null : $desc;
        }

        foreach (['price', 'compare_at_price'] as $k) {
            if (!$partial || array_key_exists($k, $input)) {
                $val = $input[$k] ?? null;
                $input[$k] = $val === null ? null : (float) $val;
            }
        }

        if (!$partial || array_key_exists('cost_price', $input)) {
            $val = $input['cost_price'] ?? null;
            $input['cost_price'] = $val === null ? null : (float) $val;
        }

        if (!$partial || array_key_exists('currency', $input)) {
            $input['currency'] = Str::upper(trim((string) ($input['currency'] ?? 'VND')));
        }

        if (!$partial || array_key_exists('stock_qty', $input)) {
            $input['stock_qty'] = (int) ($input['stock_qty'] ?? 0);
        }

        if (!$partial || array_key_exists('is_active', $input)) {
            $input['is_active'] = (bool) ($input['is_active'] ?? true);
        }

        return [$input, $categoryIds];
    }

    /**
     * @param list<int> $categoryIds
     * @return array<int, array{shop_id: int}>
     */
    private function pivotForShop(array $categoryIds, int $shopId): array
    {
        $out = [];
        foreach ($categoryIds as $id) {
            $out[(int) $id] = ['shop_id' => $shopId];
        }
        return $out;
    }
}

<?php

namespace Modules\Ecommerce\Application\Services;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Ecommerce\Application\Contracts\CategoryServiceInterface;
use Modules\Ecommerce\Domain\Models\Category;
use Modules\Ecommerce\Infrastructure\Contracts\CategoryRepositoryInterface;
use Modules\Ecommerce\Support\ShopResolver;

final class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
    ) {}

    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->categories->paginate($params);
    }

    public function getById(int $id): Category
    {
        return $this->categories->findOrFail($id);
    }

    public function create(array $input): Category
    {
        $input['shop_id'] = ShopResolver::id();
        $input = $this->normalize($input);

        if ($this->categories->existsBySlug((string) $input['slug'])) {
            throw ApiException::unprocessable(
                ErrorCode::VALIDATION_ERROR->value,
                __('messages.validation_error'),
                ['slug' => ['Slug already exists']],
            );
        }

        return DB::transaction(fn () => $this->categories->create($input));
    }

    public function update(int $id, array $input): Category
    {
        $category = $this->categories->findOrFail($id);

        $input = $this->normalize($input, partial: true);

        if (array_key_exists('slug', $input) && $input['slug'] !== null) {
            $slug = (string) $input['slug'];
            if ($this->categories->existsBySlug($slug, exceptId: $category->id)) {
                throw ApiException::unprocessable(
                    ErrorCode::VALIDATION_ERROR->value,
                    __('messages.validation_error'),
                    ['slug' => ['Slug already exists']],
                );
            }
        }

        return DB::transaction(fn () => $this->categories->update($category, $input));
    }

    public function delete(int $id): void
    {
        $category = $this->categories->findOrFail($id);

        DB::transaction(fn () => $this->categories->delete($category));
    }

    private function normalize(array $input, bool $partial = false): array
    {
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

        if (array_key_exists('parent_id', $input)) {
            $input['parent_id'] = $input['parent_id'] !== null ? (int) $input['parent_id'] : null;
        }

        if (array_key_exists('position', $input)) {
            $input['position'] = (int) $input['position'];
        }

        if (array_key_exists('is_active', $input)) {
            $input['is_active'] = (bool) $input['is_active'];
        }

        return $input;
    }
}

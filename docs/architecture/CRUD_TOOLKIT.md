# CRUD Toolkit (Filter/Sort/Include/Paginate)

Mục tiêu: tái sử dụng cho mọi dự án để tạo nhanh endpoint dạng list, có filter/sort/include/pagination, và response chuẩn.

## Thành phần core

- Parse query params: `app/Core/Support/Query/ApiQueryParams.php`
- Apply query cho Eloquent: `app/Core/Infrastructure/Query/ApiQueryApplier.php`
- Base FormRequest API: `app/Core/Http/Requests/ApiFormRequest.php`
- Pagination meta: `app/Core/Support/Pagination/PaginationMeta.php`
- Response helper: `app/Core/Http/Responses/ApiResponse.php` (`paginated()`)
- Repository building blocks:
- `app/Core/Infrastructure/Repository/Contracts/RepositoryInterface.php`
- `app/Core/Infrastructure/Repository/EloquentRepository.php`
- `app/Core/Infrastructure/Repository/CachedRepository.php`

## Convention query params

- `filters[field]=value` (khuyến nghị)
- `filter[field]=value` (tương thích ngược)
- `sort=field,-created_at`
- `include=category,brand`
- `page=1`
- `per_page=20`

## Ví dụ list endpoint (Eloquent)

Ví dụ với bảng `products`:

```php
use App\Core\Http\Responses\ApiResponse;
use App\Core\Infrastructure\Query\ApiQueryApplier;
use Modules\Catalog\Domain\Models\Product;

$params = \App\Core\Support\Query\ApiQueryParams::fromRequest(request());

$query = Product::query();

ApiQueryApplier::apply(
    query: $query,
    params: $params,
    allowedFilters: [
        'name' => ApiQueryApplier::FILTER_LIKE,
        'category_id' => ApiQueryApplier::FILTER_EXACT,
        'status' => ApiQueryApplier::FILTER_IN,
        // Khoảng thời gian / range:
        'created_at' => ApiQueryApplier::FILTER_RANGE,
    ],
    allowedSorts: ['id', 'name', 'created_at'],
    allowedIncludes: ['category'],
    defaultSorts: ['-id'],
);

$paginator = $query->paginate(perPage: $params->perPage, page: $params->page);

return ApiResponse::paginated(
    paginator: $paginator,
    items: \Modules\Catalog\Http\Resources\Api\V1\ProductResource::collection($paginator->items()),
    code: 'PRODUCT_LIST_SUCCESS',
    message: 'Lấy danh sách sản phẩm thành công',
);
```

## Ghi chú

- Bạn phải truyền allow-list (`allowedFilters`, `allowedSorts`, `allowedIncludes`). Mặc định ignore các field không được phép.
- Với filter `in`, client có thể gửi `filter[status]=active,inactive` hoặc `filter[status][]=active&filter[status][]=inactive`.
- Nếu dự án cần filter phức tạp hơn (range, date, json column), hãy extend `ApiQueryApplier` hoặc viết applier riêng trong module.
- Với filter `range`, client có thể gửi:
- `filters[created_at]=2026-01-01,2026-01-31`
- hoặc `filters[created_at][from]=2026-01-01&filters[created_at][to]=2026-01-31`

## Ví dụ repository (khuyến nghị)

Trong module, bạn vẫn nên có interface riêng, ví dụ:

- `Modules/Catalog/app/Infrastructure/Contracts/ProductRepositoryInterface.php`

Implementation Eloquent có thể extend core `EloquentRepository`, và cache decorator có thể extend core `CachedRepository` để giảm boilerplate.

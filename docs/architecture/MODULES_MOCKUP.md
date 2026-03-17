# Module Structure + Examples (Mockup)

Muc tieu:
- Xay core Laravel de lam API theo module (nwidart/laravel-modules)
- Moi module co du layer: Http (Controller/Request/Resource), Application (Service/DTO), Infrastructure (Repository/Cache), Contracts (Interface)
- Response va error schema thong nhat tren toan he thong

## 1) High-level directory tree

De xuat:
```txt
.
├── app/
│   └── Core/
│       ├── Contracts/
│       │   ├── RepositoryInterface.php
│       │   └── ServiceInterface.php
│       ├── Exceptions/
│       │   ├── ApiException.php
│       │   └── ErrorCode.php
│       ├── Http/
│       │   └── Responses/
│       │       └── ApiResponse.php
│       ├── Repositories/
│       │   ├── Cache/
│       │   │   ├── CacheKey.php
│       │   │   └── CacheRepositoryDecorator.php
│       │   └── Eloquent/
│       │       └── BaseEloquentRepository.php
│       └── Support/
│           └── Pagination.php
├── Modules/
│   ├── Auth/
│   │   ├── Routes/
│   │   │   └── api.php
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Api/V1/AuthController.php
│   │   │   └── Requests/
│   │   │       └── Api/V1/
│   │   │           ├── LoginRequest.php
│   │   │           └── RegisterRequest.php
│   │   ├── Application/
│   │   │   ├── Contracts/
│   │   │   │   └── AuthServiceInterface.php
│   │   │   └── Services/
│   │   │       └── AuthService.php
│   │   └── Providers/
│   │       └── AuthServiceProvider.php
│   ├── Catalog/
│   │   ├── Database/
│   │   │   └── Migrations/
│   │   ├── Routes/
│   │   │   └── api.php
│   │   ├── Domain/
│   │   │   └── Models/
│   │   │       ├── Category.php
│   │   │       └── Product.php
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Api/V1/
│   │   │   │       ├── CategoryController.php
│   │   │   │       └── ProductController.php
│   │   │   ├── Requests/
│   │   │   │   └── Api/V1/
│   │   │   │       ├── ProductIndexRequest.php
│   │   │   │       └── ProductStoreRequest.php
│   │   │   └── Resources/
│   │   │       └── Api/V1/
│   │   │           ├── CategoryResource.php
│   │   │           └── ProductResource.php
│   │   ├── Application/
│   │   │   ├── Contracts/
│   │   │   │   └── ProductServiceInterface.php
│   │   │   ├── DTO/
│   │   │   │   └── ProductIndexData.php
│   │   │   └── Services/
│   │   │       └── ProductService.php
│   │   ├── Infrastructure/
│   │   │   ├── Contracts/
│   │   │   │   ├── CategoryRepositoryInterface.php
│   │   │   │   └── ProductRepositoryInterface.php
│   │   │   └── Repositories/
│   │   │       ├── CachedProductRepository.php
│   │   │       ├── EloquentCategoryRepository.php
│   │   │       └── EloquentProductRepository.php
│   │   └── Providers/
│   │       └── CatalogServiceProvider.php
│   ├── Order/
│   │   └── Infrastructure/
│   │       ├── Clients/
│   │       │   └── PaymentClient.php
│   │       └── Contracts/
│   │           └── PaymentClientInterface.php
│   ├── User/
│   │   ├── Config/
│   │   │   └── config.php
│   │   ├── Database/
│   │   │   ├── Migrations/
│   │   │   └── Seeders/
│   │   ├── Routes/
│   │   │   └── api.php
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Api/V1/UserController.php
│   │   │   ├── Requests/
│   │   │   │   └── Api/V1/UserIndexRequest.php
│   │   │   └── Resources/
│   │   │       └── Api/V1/UserResource.php
│   │   ├── Application/
│   │   │   ├── Contracts/
│   │   │   │   └── UserServiceInterface.php
│   │   │   ├── DTO/
│   │   │   │   └── UserIndexData.php
│   │   │   └── Services/
│   │   │       └── UserService.php
│   │   ├── Infrastructure/
│   │   │   ├── Contracts/
│   │   │   │   └── UserRepositoryInterface.php
│   │   │   └── Repositories/
│   │   │       ├── CachedUserRepository.php
│   │   │       └── EloquentUserRepository.php
│   │   └── Providers/
│   │       ├── RouteServiceProvider.php
│   │       └── UserServiceProvider.php
│   └── ...
└── config/
    └── core.php
```

Goi y naming:
- HTTP layer: `Modules/<M>/Http/...`
- Service layer: `Modules/<M>/Application/Services/...`
- Repository layer: `Modules/<M>/Infrastructure/Repositories/...`
- Interface: `.../Contracts/...Interface.php`

## 2) API response schema (goi y)

Schema (JSend):
```json
{
  "status": "success",
  "code": "SUCCESS",
  "message": "OK",
  "data": {}
}
```

Fail (client error):
```json
{
  "status": "fail",
  "code": "VALIDATION_ERROR",
  "message": "Dữ liệu không hợp lệ",
  "data": {}
}
```

Error (server error):
```json
{
  "status": "error",
  "code": "ERROR",
  "message": "Server error",
  "data": {}
}
```

## 3) Core code mockup

### 3.1 app/Core/Http/Responses/ApiResponse.php
```php
<?php

namespace App\Core\Http\Responses;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    public static function success(mixed $data = null, string $code = 'SUCCESS', string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function fail(array $data, string $code = 'FAIL', string $message = 'Request failed', int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'fail',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error(string $message = 'Server error', ?string $code = 'ERROR', ?array $data = null, int $status = 500): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
```

### 3.2 app/Core/Repositories/Cache/CacheKey.php
```php
<?php

namespace App\Core\Repositories\Cache;

final class CacheKey
{
    public static function make(string $module, string $resource, string $action, array $params = []): string
    {
        ksort($params);
        $hash = sha1(json_encode($params));

        return "{$module}:{$resource}:{$action}:{$hash}";
    }
}
```

### 3.3 app/Core/Repositories/Cache/CacheRepositoryDecorator.php
```php
<?php

namespace App\Core\Repositories\Cache;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

abstract class CacheRepositoryDecorator
{
    public function __construct(
        protected readonly CacheRepository $cache
    ) {}

    protected function remember(string $key, int $ttlSeconds, callable $callback): mixed
    {
        // Neu dung Redis + tags: co the thay bang Cache::tags([...])->remember(...)
        return $this->cache->remember($key, $ttlSeconds, $callback);
    }

    protected function forget(string $key): void
    {
        $this->cache->forget($key);
    }
}
```

## 4) Module User mockup (end-to-end)

### 4.1 Modules/User/Routes/api.php
```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\V1\UserController;

Route::prefix('api/v1')->group(function () {
    Route::get('users', [UserController::class, 'index']);
});
```

### 4.2 Modules/User/Http/Requests/Api/V1/UserIndexRequest.php
```php
<?php

namespace Modules\User\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class UserIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function data(): array
    {
        return $this->validated();
    }
}
```

### 4.3 Modules/User/Application/DTO/UserIndexData.php
```php
<?php

namespace Modules\User\Application\DTO;

final class UserIndexData
{
    public function __construct(
        public readonly ?string $q,
        public readonly int $page,
        public readonly int $perPage
    ) {}

    public static function from(array $input): self
    {
        return new self(
            q: $input['q'] ?? null,
            page: (int)($input['page'] ?? 1),
            perPage: (int)($input['per_page'] ?? 20),
        );
    }

    public function toArray(): array
    {
        return [
            'q' => $this->q,
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
```

### 4.4 Modules/User/Infrastructure/Contracts/UserRepositoryInterface.php
```php
<?php

namespace Modules\User\Infrastructure\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Application\DTO\UserIndexData;

interface UserRepositoryInterface
{
    public function paginate(UserIndexData $data): LengthAwarePaginator;
}
```

### 4.5 Modules/User/Infrastructure/Repositories/EloquentUserRepository.php
```php
<?php

namespace Modules\User\Infrastructure\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Application\DTO\UserIndexData;
use Modules\User\Infrastructure\Contracts\UserRepositoryInterface;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function paginate(UserIndexData $data): LengthAwarePaginator
    {
        // Vi du: dung model User cua Laravel (App\\Models\\User) hoac model module (Modules\\User\\Domain\\Models\\User)
        $query = \App\Models\User::query();

        if ($data->q) {
            $query->where(function ($q) use ($data) {
                $q->where('name', 'like', "%{$data->q}%")
                  ->orWhere('email', 'like', "%{$data->q}%");
            });
        }

        return $query
            ->orderByDesc('id')
            ->paginate($data->perPage, ['*'], 'page', $data->page);
    }
}
```

### 4.6 Modules/User/Infrastructure/Repositories/CachedUserRepository.php
```php
<?php

namespace Modules\User\Infrastructure\Repositories;

use App\Core\Repositories\Cache\CacheKey;
use App\Core\Repositories\Cache\CacheRepositoryDecorator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Modules\User\Application\DTO\UserIndexData;
use Modules\User\Infrastructure\Contracts\UserRepositoryInterface;

final class CachedUserRepository extends CacheRepositoryDecorator implements UserRepositoryInterface
{
    public function __construct(
        CacheRepository $cache,
        private readonly UserRepositoryInterface $inner
    ) {
        parent::__construct($cache);
    }

    public function paginate(UserIndexData $data): LengthAwarePaginator
    {
        $key = CacheKey::make('user', 'users', 'paginate', $data->toArray());
        $ttl = config('user.cache.users_paginate_ttl', 60);

        return $this->remember($key, $ttl, fn () => $this->inner->paginate($data));
    }
}
```

### 4.7 Modules/User/Application/Contracts/UserServiceInterface.php
```php
<?php

namespace Modules\User\Application\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Application\DTO\UserIndexData;

interface UserServiceInterface
{
    public function paginate(UserIndexData $data): LengthAwarePaginator;
}
```

### 4.8 Modules/User/Application/Services/UserService.php
```php
<?php

namespace Modules\User\Application\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Application\Contracts\UserServiceInterface;
use Modules\User\Application\DTO\UserIndexData;
use Modules\User\Infrastructure\Contracts\UserRepositoryInterface;

final class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $users
    ) {}

    public function paginate(UserIndexData $data): LengthAwarePaginator
    {
        return $this->users->paginate($data);
    }
}
```

### 4.9 Modules/User/Http/Resources/Api/V1/UserResource.php
```php
<?php

namespace Modules\User\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
```

### 4.10 Modules/User/Http/Controllers/Api/V1/UserController.php
```php
<?php

namespace Modules\User\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\User\Application\Contracts\UserServiceInterface;
use Modules\User\Application\DTO\UserIndexData;
use Modules\User\Http\Requests\Api\V1\UserIndexRequest;
use Modules\User\Http\Resources\Api\V1\UserResource;

final class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $users
    ) {}

    public function index(UserIndexRequest $request)
    {
        $data = UserIndexData::from($request->data());
        $paginator = $this->users->paginate($data);

        return ApiResponse::success([
            'items' => UserResource::collection($paginator->getCollection()),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
```

### 4.11 Modules/User/Providers/UserServiceProvider.php (binding)
```php
<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\User\Application\Contracts\UserServiceInterface;
use Modules\User\Application\Services\UserService;
use Modules\User\Infrastructure\Contracts\UserRepositoryInterface;
use Modules\User\Infrastructure\Repositories\CachedUserRepository;
use Modules\User\Infrastructure\Repositories\EloquentUserRepository;

final class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);

        // Repository: Cache decorator bọc Eloquent repo
        $this->app->bind(UserRepositoryInterface::class, function ($app) {
            return new CachedUserRepository(
                cache()->store(),
                $app->make(EloquentUserRepository::class),
            );
        });
    }
}
```

## 5) Module config (cache TTL)

### Modules/User/Config/config.php
```php
<?php

return [
    'cache' => [
        'users_paginate_ttl' => 60,
    ],
];
```

## 6) Notes / conventions

- Cache tags: neu ban dung Redis, co the nang cap `CacheRepositoryDecorator` de dung `Cache::tags([...])` cho invalidate theo module/resource.
- DTO: giup tach logic parse request (Http) khoi service/repository.
- Repository interface: giup thay implementation (Eloquent, API client, raw SQL) va de test.
- Service: chua use-case, transaction, orchestration giua nhieu repositories.

## 7) Vi du domain: Categories (1) - (N) Products

### 7.0 Vi sao minh dua vao module `Catalog`?

Ly do:
- `Catalog` la bounded context rat pho bien trong e-commerce: quan ly danh muc, san pham, thuoc tinh, gia, ton kho (tuy scope).
- Tuyen bo module ro rang giup ban hinh dung: 1 module so huu DB tables + Models + Repositories + Services + API endpoints cua no.
- Sau nay `Order` can "doc" thong tin product/category (ten, sku, price) thi co 2 cach:
  - Monolith: `Order` co the query truc tiep DB, nhung nen gioi han o muc "read model" va han che phu thuoc chieu nguoc.
  - Microservice: `Order` chi luu snapshot can thiet vao `order_items` (sku/name/price) va goi `Catalog service` de validate/lay data khi can.

Muc tieu:
- `categories` 1-n `products` (moi product thuoc 1 category)
- API danh sach products co filter theo `category_id`, search `q`, pagination
- Khi CRUD product/category thi cache can duoc invalidate (theo key hoac theo tag)

### 7.1 Database tables (goi y)

`categories`:
- `id` bigint PK
- `name` varchar(150)
- `slug` varchar(180) unique
- `is_active` tinyint default 1
- `created_at`, `updated_at`

`products`:
- `id` bigint PK
- `category_id` bigint FK -> categories.id (index)
- `sku` varchar(64) unique
- `name` varchar(180)
- `slug` varchar(200) unique
- `price` decimal(12,2)
- `status` varchar(30) (vd: draft/active/archived)
- `created_at`, `updated_at`

### 7.2 Migration mockup

`Modules/Catalog/Database/Migrations/xxxx_xx_xx_create_categories_table.php`
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 150);
    $table->string('slug', 180)->unique();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

`Modules/Catalog/Database/Migrations/xxxx_xx_xx_create_products_table.php`
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained('categories');
    $table->string('sku', 64)->unique();
    $table->string('name', 180);
    $table->string('slug', 200)->unique();
    $table->decimal('price', 12, 2);
    $table->string('status', 30)->default('active');
    $table->timestamps();

    $table->index(['category_id', 'status']);
});
```

### 7.3 Eloquent models (relation)

`Modules/Catalog/Domain/Models/Category.php`
```php
<?php

namespace Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Category extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
```

`Modules/Catalog/Domain/Models/Product.php`
```php
<?php

namespace Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Product extends Model
{
    protected $fillable = ['category_id', 'sku', 'name', 'slug', 'price', 'status'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
```

### 7.4 Routes (API)

`Modules/Catalog/Routes/api.php`
```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\Api\V1\CategoryController;
use Modules\Catalog\Http\Controllers\Api\V1\ProductController;

Route::prefix('api/v1')->group(function () {
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']); // can auth middleware neu can
});
```

### 7.5 Product list DTO + repository filter

`Modules/Catalog/Application/DTO/ProductIndexData.php`
```php
<?php

namespace Modules\Catalog\Application\DTO;

final class ProductIndexData
{
    public function __construct(
        public readonly ?int $categoryId,
        public readonly ?string $q,
        public readonly int $page,
        public readonly int $perPage
    ) {}

    public static function from(array $input): self
    {
        return new self(
            categoryId: isset($input['category_id']) ? (int)$input['category_id'] : null,
            q: $input['q'] ?? null,
            page: (int)($input['page'] ?? 1),
            perPage: (int)($input['per_page'] ?? 20),
        );
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->categoryId,
            'q' => $this->q,
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }
}
```

`Modules/Catalog/Infrastructure/Contracts/ProductRepositoryInterface.php`
```php
<?php

namespace Modules\Catalog\Infrastructure\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Catalog\Application\DTO\ProductIndexData;

interface ProductRepositoryInterface
{
    public function paginate(ProductIndexData $data): LengthAwarePaginator;
    public function create(array $attributes);
}
```

`Modules/Catalog/Infrastructure/Repositories/EloquentProductRepository.php`
```php
<?php

namespace Modules\Catalog\Infrastructure\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Catalog\Application\DTO\ProductIndexData;
use Modules\Catalog\Domain\Models\Product;
use Modules\Catalog\Infrastructure\Contracts\ProductRepositoryInterface;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function paginate(ProductIndexData $data): LengthAwarePaginator
    {
        $query = Product::query()->with('category:id,name,slug');

        if ($data->categoryId) {
            $query->where('category_id', $data->categoryId);
        }

        if ($data->q) {
            $query->where(function ($q) use ($data) {
                $q->where('name', 'like', "%{$data->q}%")
                  ->orWhere('sku', 'like', "%{$data->q}%");
            });
        }

        return $query
            ->orderByDesc('id')
            ->paginate($data->perPage, ['*'], 'page', $data->page);
    }

    public function create(array $attributes)
    {
        return Product::create($attributes);
    }
}
```

`Modules/Catalog/Infrastructure/Repositories/CachedProductRepository.php`
```php
<?php

namespace Modules\Catalog\Infrastructure\Repositories;

use App\Core\Repositories\Cache\CacheKey;
use App\Core\Repositories\Cache\CacheRepositoryDecorator;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Catalog\Application\DTO\ProductIndexData;
use Modules\Catalog\Infrastructure\Contracts\ProductRepositoryInterface;

final class CachedProductRepository extends CacheRepositoryDecorator implements ProductRepositoryInterface
{
    public function __construct(
        CacheRepository $cache,
        private readonly ProductRepositoryInterface $inner
    ) {
        parent::__construct($cache);
    }

    public function paginate(ProductIndexData $data): LengthAwarePaginator
    {
        $key = CacheKey::make('catalog', 'products', 'paginate', $data->toArray());
        $ttl = config('catalog.cache.products_paginate_ttl', 60);

        return $this->remember($key, $ttl, fn () => $this->inner->paginate($data));
    }

    public function create(array $attributes)
    {
        // Tao moi product: cach don gian nhat la xoa cac key lien quan.
        // Neu dung Redis + tags: nen xoa theo tag (vd: tag catalog:products) de khoi can track tung key.
        return $this->inner->create($attributes);
    }
}
```

### 7.6 Product service (validation use-case)

Y tuong:
- Service check category ton tai (hoac validate truoc o request)
- Service lam transaction neu co nhieu thao tac
- Service la noi dat logic: status mac dinh, generate slug, emit event de invalidate cache, ...

## 8) Auth module (Register/Login) cho API

De hieu don gian, vi du theo Laravel Sanctum:
- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout` (auth:sanctum)
- `GET /api/v1/auth/me` (auth:sanctum)

`Modules/Auth/Routes/api.php`
```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Api\V1\AuthController;

Route::prefix('api/v1/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
```

`Modules/Auth/Http/Controllers/Api/V1/AuthController.php` (mockup)
```php
<?php

namespace Modules\Auth\Http\Controllers\Api\V1;

use App\Core\Http\Responses\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Auth\Application\Contracts\AuthServiceInterface;
use Modules\Auth\Http\Requests\Api\V1\LoginRequest;
use Modules\Auth\Http\Requests\Api\V1\RegisterRequest;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $auth
    ) {}

    public function register(RegisterRequest $request)
    {
        $result = $this->auth->register($request->validated());
        return ApiResponse::success($result, status: 200);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->auth->login($request->validated());
        return ApiResponse::success($result);
    }

    public function me()
    {
        return ApiResponse::success(['user' => request()->user()]);
    }

    public function logout()
    {
        $this->auth->logout(request()->user());
        return ApiResponse::success(null);
    }
}
```

`Modules/Auth/Application/Services/AuthService.php` (mockup)
```php
<?php

namespace Modules\Auth\Application\Services;

use Illuminate\Support\Facades\Hash;
use Modules\Auth\Application\Contracts\AuthServiceInterface;

final class AuthService implements AuthServiceInterface
{
    public function register(array $input): array
    {
        $user = \App\Models\User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $input): array
    {
        $user = \App\Models\User::where('email', $input['email'])->first();

        if (!$user || !Hash::check($input['password'], $user->password)) {
            // Thuc te: nen throw ApiException voi ErrorCode
            abort(422, 'Invalid credentials');
        }

        return [
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ];
    }

    public function logout($user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
```

## 9) Goi sang microservice khac: to chuc code the nao?

Nguyen tac:
- Module nao can goi microservice thi module do so huu `Contracts` + `Client` (khong dat client o controller)
- Client chi lam I/O (HTTP/gRPC), mapping response -> DTO/array, throw exception co y nghia
- Service dung client de hoan thanh use-case, co retry/timeouts, va co phuong an async neu can

### 9.1 Vi du: Order goi Payment service (HTTP)

`Modules/Order/Infrastructure/Contracts/PaymentClientInterface.php`
```php
<?php

namespace Modules\Order\Infrastructure\Contracts;

interface PaymentClientInterface
{
    public function createPayment(array $payload): array;
}
```

`Modules/Order/Infrastructure/Clients/PaymentClient.php`
```php
<?php

namespace Modules\Order\Infrastructure\Clients;

use Illuminate\Support\Facades\Http;
use Modules\Order\Infrastructure\Contracts\PaymentClientInterface;

final class PaymentClient implements PaymentClientInterface
{
    public function createPayment(array $payload): array
    {
        $baseUrl = config('services.payment.base_url');
        $token = config('services.payment.token');

        $response = Http::baseUrl($baseUrl)
            ->withToken($token)
            ->timeout(3)
            ->retry(2, 200)
            ->post('/v1/payments', $payload);

        if ($response->failed()) {
            // Thuc te: nen throw exception rieng (PaymentServiceUnavailable, PaymentRejected, ...)
            abort(502, 'Payment service error');
        }

        return $response->json();
    }
}
```

### 9.2 Sync vs Async

- Sync (request/response): dung khi user can ket qua ngay (vd tao payment url).
- Async (queue/job/outbox): dung khi microservice co the cham, hoac muon retry doc lap.
  - Order module tao record "outbox" (event) + commit transaction
  - Job doc outbox va call PaymentClient, retry theo backoff

## 10) Vi du luong Don hang (Order) voi nhieu bang lien ket

Muc tieu:
- Thiet ke module `Order` de sau nay mo rong duoc (nhieu bang, nhieu trang thai, goi payment/shipping service).
- Khi tao don: can transaction (order + items + address) va co cach xu ly call microservice an toan (outbox).

### 10.1 Database tables (goi y)

`orders`:
- `id` bigint PK
- `code` varchar(32) unique (vd: OD20260317xxxx)
- `user_id` bigint index
- `status` varchar(30) (pending/confirmed/paid/cancelled/fulfilled/...)
- `currency` char(3) (VND/USD)
- `subtotal` decimal(12,2)
- `discount_total` decimal(12,2)
- `shipping_fee` decimal(12,2)
- `grand_total` decimal(12,2)
- `created_at`, `updated_at`

`order_items`:
- `id` bigint PK
- `order_id` bigint FK -> orders.id (index)
- `product_id` bigint nullable (neu microservice thi co the null)
- `sku` varchar(64)
- `name` varchar(180)
- `unit_price` decimal(12,2)
- `qty` int
- `line_total` decimal(12,2)

`order_addresses`:
- `id` bigint PK
- `order_id` bigint FK -> orders.id unique (1-1)
- `type` varchar(20) (shipping/billing)
- `full_name`, `phone`, `address1`, `ward`, `district`, `province`, `country`

`order_payments`:
- `id` bigint PK
- `order_id` bigint FK -> orders.id (index)
- `provider` varchar(30) (momo/vnpay/stripe/...)
- `status` varchar(30) (init/pending/succeeded/failed)
- `amount` decimal(12,2)
- `payment_ref` varchar(100) nullable
- `paid_at` datetime nullable

`outbox_messages` (neu muon async microservice):
- `id` bigint PK
- `aggregate_type` varchar(50) (Order)
- `aggregate_id` bigint (order_id)
- `type` varchar(80) (PaymentRequested, ...)
- `payload` json
- `status` varchar(20) (pending/processing/done/failed)
- `attempts` int default 0
- `available_at` datetime
- timestamps

### 10.2 Quan he (1-n / 1-1)

- `Order` hasMany `OrderItem`
- `Order` hasOne `OrderAddress` (hoac 2 record: shipping + billing)
- `Order` hasMany `OrderPayment` (de support retry/partial)

### 10.3 Module tree (goi y) cho Order

```txt
Modules/Order/
├── Database/Migrations/
├── Domain/Models/
│   ├── Order.php
│   ├── OrderItem.php
│   ├── OrderAddress.php
│   └── OrderPayment.php
├── Http/
│   ├── Controllers/Api/V1/OrderController.php
│   ├── Requests/Api/V1/OrderStoreRequest.php
│   └── Resources/Api/V1/OrderResource.php
├── Application/
│   ├── DTO/
│   │   └── CreateOrderData.php
│   ├── Contracts/
│   │   └── OrderServiceInterface.php
│   └── Services/
│       └── OrderService.php
└── Infrastructure/
    ├── Contracts/
    │   ├── OrderRepositoryInterface.php
    │   └── PaymentClientInterface.php
    ├── Repositories/
    │   └── EloquentOrderRepository.php
    └── Clients/
        └── PaymentClient.php
```

### 10.4 Luong tao don (sync) vs (async)

Sync (don gian, de hieu):
1) `OrderController@store` validate request
2) `OrderService::create($data)` mo transaction
3) Insert `orders`, `order_items`, `order_addresses`
4) Commit transaction
5) Goi `PaymentClient->createPayment(...)` va luu `order_payments`

Rui ro sync:
- Neu payment service cham/loi: request tao don co the bi fail du order da tao (can design idempotency + retry).

Async (khuyen nghi neu co microservice):
1) `OrderService::create($data)` transaction:
   - Insert `orders`, `order_items`, `order_addresses`
   - Insert `outbox_messages` (PaymentRequested)
2) Commit
3) Job/worker doc outbox va call `PaymentClient`
4) Cap nhat `order_payments` + `orders.status` theo ket qua

### 10.5 Nguyen tac quan trong khi nhieu bang lien ket

- Transaction boundary: tao order + items + address nen nam chung transaction.
- Snapshot data: `order_items` nen luu snapshot `sku/name/unit_price` (tranh lech neu product thay doi).
- Idempotency: tao don va/hoac call payment nen co idempotency key (vd `order.code`) de retry an toan.
- Read model: list orders nen join den items/address theo nhu cau; neu performance can thi tao view/materialized cache.

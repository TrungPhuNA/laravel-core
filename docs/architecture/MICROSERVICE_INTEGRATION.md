# Tích hợp Microservice (HTTP Client)

Tài liệu này mô tả cách gọi API sang microservice khác theo chuẩn của core-template.

## Mục tiêu

- Chuẩn hoá timeout, retry, header, log.
- Luôn có `trace_id` để trace giữa nhiều service.
- Khi microservice lỗi, API vẫn trả response chuẩn (JSend) thông qua `ApiException`.

## Thành phần core

- HTTP client wrapper: `app/Core/Infrastructure/Http/CoreHttpClient.php`
- Middleware tạo trace id: `app/Core/Http/Middleware/RequestId.php`
- Config: `config/core.php` (nhóm `http`)

## Env config thường dùng

- `CORE_HTTP_TIMEOUT` (giây)
- `CORE_HTTP_CONNECT_TIMEOUT` (giây)
- `CORE_HTTP_RETRY_TIMES` (số lần retry, không tính lần gọi đầu)
- `CORE_HTTP_RETRY_SLEEP_MS` (milliseconds)
- `CORE_HTTP_USER_AGENT`
- `CORE_HTTP_LOG` (`true|false`)
- `CORE_HTTP_LOG_MAX_BODY` (giới hạn body log)

## Quy ước header khi gọi microservice

- `Accept: application/json`
- `X-Request-Id`: lấy từ request hiện tại (hoặc server tự generate)
- `X-Locale`: `vi|en` theo locale hiện tại
- `User-Agent`: lấy từ `CORE_HTTP_USER_AGENT`

## Cấu trúc code trong module

Khuyến nghị đặt client ở:

- `Modules/{Module}/app/Infrastructure/Clients/*`

Mỗi client nên:

- Có `serviceName` rõ ràng (ví dụ `catalog-service`).
- Nhận `baseUrl` từ env/config.
- Chỉ expose method đúng nghiệp vụ (không expose raw `request()` khắp nơi).

## Ví dụ client

```php
<?php

namespace Modules\Order\Infrastructure\Clients;

use App\Core\Infrastructure\Http\CoreHttpClient;

final class CatalogClient
{
    private CoreHttpClient $http;

    public function __construct()
    {
        $this->http = new CoreHttpClient(
            serviceName: 'catalog-service',
            baseUrl: (string) config('services.catalog.base_url'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getProduct(string $id): array
    {
        return $this->http->requestJson('GET', "v1/products/{$id}");
    }
}
```

Gợi ý config:

```php
// config/services.php
return [
    'catalog' => [
        'base_url' => env('CATALOG_SERVICE_URL', 'http://catalog-service.internal'),
    ],
];
```

## Mapping lỗi

`CoreHttpClient` sẽ map các trường hợp chung:

- Timeout / connection error: `MICROSERVICE_UNAVAILABLE` (HTTP 502)
- Microservice trả non-2xx: `MICROSERVICE_ERROR` (HTTP 502)
- Microservice trả body không phải JSON khi bạn gọi `requestJson()`: `MICROSERVICE_BAD_RESPONSE` (HTTP 502)

Nếu module cần mapping đặc thù (ví dụ microservice trả 404 thì bạn muốn map về `NOT_FOUND` của API), hãy catch `ApiException` hoặc kiểm tra `Response` bằng `request()` và tự xử lý trong client/service của module.

## Logging và mask dữ liệu nhạy cảm

Mặc định sẽ log theo channel Laravel với message:

- `microservice.response`
- `microservice.failure`

Mask key nhạy cảm cấu hình ở `config/core.php` (`http.log.masked_keys`).

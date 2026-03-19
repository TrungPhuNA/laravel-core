<?php

namespace Modules\Webhook\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class WebhookIndexRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'filters' => ['sometimes', 'array'],
            'filter' => ['sometimes', 'array'], // backward compatible
            'sort' => ['sometimes', 'nullable', 'string'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:200'],
        ];
    }

    public function queryParameters(): array
    {
        return [
            'filters[name]' => [
                'description' => 'Lọc theo tên webhook (LIKE).',
                'example' => 'payment',
            ],
            'filters[is_active]' => [
                'description' => 'Lọc theo trạng thái (0/1).',
                'example' => 1,
            ],
            'filters[auth_type]' => [
                'description' => 'Lọc theo auth_type: none|token|hmac.',
                'example' => 'hmac',
            ],
            'filters[created_at]' => [
                'description' => 'Khoảng thời gian tạo (from,to).',
                'example' => '2026-03-01,2026-03-31',
            ],
            'sort' => [
                'description' => 'Sắp xếp. Mặc định -id.',
                'example' => '-id',
            ],
            'page' => [
                'description' => 'Trang hiện tại.',
                'example' => 1,
            ],
            'per_page' => [
                'description' => 'Số item mỗi trang.',
                'example' => 20,
            ],
        ];
    }
}

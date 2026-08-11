<?php

namespace Modules\Monitor\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class DomainIndexRequest extends ApiFormRequest
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
            'filters[domain]' => [
                'description' => 'Lọc theo tên domain (LIKE).',
                'example' => 'vn',
            ],
            'filters[check_status]' => [
                'description' => 'Lọc theo trạng thái check: unknown|ok|error.',
                'example' => 'ok',
            ],
            'filters[is_active]' => [
                'description' => 'Lọc theo trạng thái theo dõi (0/1).',
                'example' => 1,
            ],
            'sort' => [
                'description' => 'Sắp xếp. Mặc định theo expires_at.',
                'example' => '-expires_at',
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
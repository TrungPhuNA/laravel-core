<?php

namespace Modules\Webhook\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class WebhookRequestIndexRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'filters' => ['sometimes', 'array'],
            'filter' => ['sometimes', 'array'],
            'sort' => ['sometimes', 'nullable', 'string'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:200'],
        ];
    }

    public function queryParameters(): array
    {
        return [
            'filters[method]' => [
                'description' => 'Lọc theo method (GET/POST).',
                'example' => 'POST',
            ],
            'filters[ip]' => [
                'description' => 'Lọc theo IP (exact).',
                'example' => '127.0.0.1',
            ],
            'filters[received_at]' => [
                'description' => 'Khoảng thời gian nhận (from,to).',
                'example' => '2026-03-01,2026-03-31',
            ],
            'sort' => [
                'description' => 'Sắp xếp. Mặc định -received_at.',
                'example' => '-received_at',
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


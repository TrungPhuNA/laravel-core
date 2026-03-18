<?php

namespace Modules\Setting\Http\Requests\Api\V1\Queue;

use App\Core\Http\Requests\ApiFormRequest;

final class QueueFailedJobIndexRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'filters' => ['sometimes', 'array'],
            'filter' => ['sometimes', 'array'],
            'sort' => ['sometimes', 'nullable', 'string'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function queryParameters(): array
    {
        return [
            'filters[queue]' => [
                'description' => 'Tên queue (exact).',
                'example' => 'default',
            ],
            'filters[connection]' => [
                'description' => 'Connection (exact).',
                'example' => 'database',
            ],
            'filters[failed_at]' => [
                'description' => 'Khoảng thời gian fail (from,to).',
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


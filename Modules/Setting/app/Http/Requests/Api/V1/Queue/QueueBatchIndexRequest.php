<?php

namespace Modules\Setting\Http\Requests\Api\V1\Queue;

use App\Core\Http\Requests\ApiFormRequest;

final class QueueBatchIndexRequest extends ApiFormRequest
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
            'filters[name]' => [
                'description' => 'Lọc theo tên batch (LIKE).',
                'example' => 'import',
            ],
            'sort' => [
                'description' => 'Sắp xếp. Mặc định -created_at.',
                'example' => '-created_at',
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


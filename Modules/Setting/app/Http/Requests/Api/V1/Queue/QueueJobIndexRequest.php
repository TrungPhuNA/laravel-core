<?php

namespace Modules\Setting\Http\Requests\Api\V1\Queue;

use App\Core\Http\Requests\ApiFormRequest;

final class QueueJobIndexRequest extends ApiFormRequest
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
            'filters[status]' => [
                'description' => 'Trạng thái: pending|reserved|delayed|all.',
                'example' => 'pending',
            ],
            'filters[created_at]' => [
                'description' => 'Khoảng thời gian create (unix timestamp) dạng "from,to".',
                'example' => '1700000000,1700100000',
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


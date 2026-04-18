<?php

namespace Modules\CheatSheet\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class PublicCheatSheetIndexRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'filters' => ['sometimes', 'array'],
            'filter' => ['sometimes', 'array'],
            'include' => ['sometimes', 'nullable', 'string'],
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
            'filters[q]' => [
                'description' => 'Search theo title/body (LIKE).',
                'example' => 'laravel',
            ],
            'filters[tag]' => [
                'description' => 'Lọc theo tag (name/slug). Có thể truyền CSV: "php,laravel".',
                'example' => 'php',
            ],
            'sort' => [
                'description' => 'Sắp xếp. Ví dụ: "-published_at,-updated_at".',
                'example' => '-published_at',
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


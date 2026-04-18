<?php

namespace Modules\CheatSheet\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class CheatSheetIndexRequest extends ApiFormRequest
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
                'description' => 'Search nhanh theo title/body (LIKE).',
                'example' => 'laravel',
            ],
            'filters[tag]' => [
                'description' => 'Lọc theo tag (slug từ name). Có thể truyền CSV: "php,laravel".',
                'example' => 'php',
            ],
            'filters[visibility]' => [
                'description' => 'Lọc theo visibility: private|unlisted|public.',
                'example' => 'private',
            ],
            'sort' => [
                'description' => 'Sắp xếp. Ví dụ: "-updated_at,-id" (mặc định), "-id", "title".',
                'example' => '-updated_at,-id',
            ],
            'page' => [
                'description' => 'Trang hiện tại.',
                'example' => 1,
            ],
            'per_page' => [
                'description' => 'Số item mỗi trang (giới hạn theo CORE_API_MAX_PER_PAGE).',
                'example' => 20,
            ],
        ];
    }
}


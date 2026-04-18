<?php

namespace Modules\CheatSheet\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class CheatSheetStoreRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'visibility' => ['sometimes', 'string', 'in:private,unlisted,public'],
            'published_at' => ['sometimes', 'nullable', 'date'],
            'tags' => ['sometimes', 'array', 'max:50'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'title' => [
                'description' => 'Tiêu đề cheat sheet.',
                'example' => 'Laravel notes',
            ],
            'body' => [
                'description' => 'Nội dung (text/markdown).',
                'example' => "Cache\\n- remember\\n- forget",
            ],
            'visibility' => [
                'description' => 'private|unlisted|public (mặc định private).',
                'example' => 'private',
            ],
            'published_at' => [
                'description' => 'Ngày/giờ publish (tuỳ chọn).',
                'example' => '2026-04-18',
            ],
            'tags' => [
                'description' => 'Danh sách tag (string).',
                'example' => ['php', 'laravel'],
            ],
        ];
    }
}


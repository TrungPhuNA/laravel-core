<?php

namespace Modules\CheatSheet\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class CheatSheetUpdateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'body' => ['sometimes', 'string'],
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
                'example' => 'Laravel notes (updated)',
            ],
            'body' => [
                'description' => 'Nội dung (text/markdown).',
                'example' => "Sanctum\\n- token\\n- middleware",
            ],
            'visibility' => [
                'description' => 'private|unlisted|public.',
                'example' => 'unlisted',
            ],
            'tags' => [
                'description' => 'Danh sách tag. Truyền mảng rỗng để xoá hết tag.',
                'example' => ['sanctum', 'auth'],
            ],
        ];
    }
}


<?php

namespace Modules\Monitor\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class DomainStoreRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'domain' => ['required', 'string', 'max:180'],
            'note' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'domain' => [
                'description' => 'Tên domain (có thể dán kèm https:// và path, hệ thống sẽ chuẩn hoá).',
                'example' => 'https://example.com/',
            ],
            'note' => [
                'description' => 'Ghi chú.',
                'example' => 'Domain chính',
            ],
            'is_active' => [
                'description' => 'Bật theo dõi.',
                'example' => true,
            ],
        ];
    }
}
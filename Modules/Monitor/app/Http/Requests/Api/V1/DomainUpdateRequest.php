<?php

namespace Modules\Monitor\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class DomainUpdateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'note' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'note' => [
                'description' => 'Ghi chú.',
                'example' => 'Domain chính',
            ],
            'is_active' => [
                'description' => 'Bật/tắt theo dõi domain.',
                'example' => true,
            ],
        ];
    }
}
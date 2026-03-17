<?php

namespace Modules\Setting\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class UpsertSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.key' => ['required', 'string', 'max:150'],
            'items.*.value' => ['present'],
            'items.*.group' => ['nullable', 'string', 'max:100'],
            'items.*.is_public' => ['nullable', 'boolean'],
            'items.*.description' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'items' => [
                'description' => 'Danh sách settings cần upsert.',
                'example' => [
                    [
                        'key' => 'site_name',
                        'value' => 'Core API',
                        'group' => 'general',
                        'is_public' => true,
                        'description' => 'Tên website',
                    ],
                ],
            ],
        ];
    }
}


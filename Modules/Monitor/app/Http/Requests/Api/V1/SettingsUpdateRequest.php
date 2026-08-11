<?php

namespace Modules\Monitor\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class SettingsUpdateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'check.rdap.enabled' => ['sometimes', 'boolean'],
            'check.whois.enabled' => ['sometimes', 'boolean'],
            'check.third_party.enabled' => ['sometimes', 'boolean'],
            'check.third_party.api_key' => ['sometimes', 'nullable', 'string', 'max:255'],
            'warning.normal_days' => ['sometimes', 'integer', 'min:1', 'max:3650'],
            'warning.soon_days' => ['sometimes', 'integer', 'min:1', 'max:3650'],
            'warning.critical_days' => ['sometimes', 'integer', 'min:1', 'max:3650'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'check.rdap.enabled' => [
                'description' => 'Bật tra cứu qua RDAP.',
                'example' => true,
            ],
            'check.whois.enabled' => [
                'description' => 'Bật tra cứu qua WHOIS (port 43).',
                'example' => true,
            ],
            'check.third_party.enabled' => [
                'description' => 'Bật fallback API bên thứ ba (cần api_key).',
                'example' => false,
            ],
            'check.third_party.api_key' => [
                'description' => 'API key cho nguồn tra cứu bên thứ ba.',
                'example' => '',
            ],
            'warning.normal_days' => [
                'description' => 'Trên ngưỡng này hiển thị màu xanh.',
                'example' => 60,
            ],
            'warning.soon_days' => [
                'description' => 'Dưới ngưỡng này hiển thị màu vàng.',
                'example' => 30,
            ],
            'warning.critical_days' => [
                'description' => 'Dưới ngưỡng này hiển thị màu cam.',
                'example' => 7,
            ],
        ];
    }
}
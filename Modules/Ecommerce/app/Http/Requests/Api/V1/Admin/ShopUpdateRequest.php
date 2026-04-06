<?php

namespace Modules\Ecommerce\Http\Requests\Api\V1\Admin;

use App\Core\Http\Requests\ApiFormRequest;

final class ShopUpdateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['sometimes', 'string', 'max:60'],
            'name' => ['sometimes', 'string', 'max:200'],
            'domain' => ['sometimes', 'nullable', 'string', 'max:200'],
            'timezone' => ['sometimes', 'string', 'max:60'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'is_active' => ['sometimes', 'boolean'],
            'meta' => ['sometimes', 'nullable', 'array'],
        ];
    }
}


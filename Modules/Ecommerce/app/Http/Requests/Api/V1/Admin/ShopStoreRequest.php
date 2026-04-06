<?php

namespace Modules\Ecommerce\Http\Requests\Api\V1\Admin;

use App\Core\Http\Requests\ApiFormRequest;

final class ShopStoreRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:60'],
            'name' => ['required', 'string', 'max:200'],
            'domain' => ['sometimes', 'nullable', 'string', 'max:200'],
            'timezone' => ['sometimes', 'string', 'max:60'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'is_active' => ['sometimes', 'boolean'],
            'meta' => ['sometimes', 'nullable', 'array'],
        ];
    }
}


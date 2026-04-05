<?php

namespace Modules\Ecommerce\Http\Requests\Api\V1\Admin;

use App\Core\Http\Requests\ApiFormRequest;

final class CustomerStoreRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:200'],
            'email' => ['sometimes', 'nullable', 'email', 'max:200'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:40'],
            'note' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}


<?php

namespace Modules\Setting\Http\Requests\Api\V1\Rbac;

use App\Core\Http\Requests\ApiFormRequest;

final class RoleStoreRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'max:150'],
        ];
    }
}


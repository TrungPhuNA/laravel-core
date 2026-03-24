<?php

namespace Modules\Setting\Http\Requests\Api\V1\Rbac;

use App\Core\Http\Requests\ApiFormRequest;

final class RoleUpdateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'max:150'],
        ];
    }
}


<?php

namespace Modules\Setting\Http\Requests\Api\V1\Rbac;

use App\Core\Http\Requests\ApiFormRequest;

final class PermissionStoreRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
        ];
    }
}


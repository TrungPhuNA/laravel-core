<?php

namespace Modules\Setting\Http\Requests\Api\V1\Rbac;

use App\Core\Http\Requests\ApiFormRequest;

final class UserSyncPermissionsRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'max:150'],
        ];
    }
}


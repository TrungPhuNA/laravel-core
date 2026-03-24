<?php

namespace Modules\Setting\Http\Requests\Api\V1\Rbac;

use App\Core\Http\Requests\ApiFormRequest;

final class UserSyncRolesRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'roles' => ['required', 'array'],
            'roles.*' => ['string', 'max:100'],
        ];
    }
}


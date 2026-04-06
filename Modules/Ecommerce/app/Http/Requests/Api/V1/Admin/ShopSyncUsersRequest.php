<?php

namespace Modules\Ecommerce\Http\Requests\Api\V1\Admin;

use App\Core\Http\Requests\ApiFormRequest;

final class ShopSyncUsersRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'members' => ['required', 'array'],
            'members.*.user_id' => ['required', 'integer', 'min:1'],
            'members.*.role' => ['sometimes', 'string', 'max:40'],
        ];
    }
}


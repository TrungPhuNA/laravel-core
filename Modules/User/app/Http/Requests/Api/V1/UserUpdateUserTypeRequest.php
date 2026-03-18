<?php

namespace Modules\User\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;
use App\Core\Support\UserType;

final class UserUpdateUserTypeRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $types = implode(',', array_map(static fn (UserType $t) => $t->value, UserType::cases()));

        return [
            'user_type' => ['required', 'string', "in:{$types}"],
        ];
    }
}


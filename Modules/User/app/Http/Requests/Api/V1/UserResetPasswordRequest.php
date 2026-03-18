<?php

namespace Modules\User\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class UserResetPasswordRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }
}


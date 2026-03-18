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

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'password' => [
                'description' => 'Mật khẩu mới (tối thiểu 8 ký tự).',
                'example' => '123456789',
            ],
            'password_confirmation' => [
                'description' => 'Nhập lại mật khẩu mới (phải giống password).',
                'example' => '123456789',
            ],
        ];
    }
}

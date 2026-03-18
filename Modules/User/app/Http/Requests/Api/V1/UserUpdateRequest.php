<?php

namespace Modules\User\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;
use App\Core\Support\UserType;

final class UserUpdateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $types = implode(',', array_map(static fn (UserType $t) => $t->value, UserType::cases()));

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'user_type' => ['sometimes', 'string', "in:{$types}"],

            // Profile fields
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'avatar_url' => ['sometimes', 'nullable', 'url'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'gender' => ['sometimes', 'nullable', 'string', 'max:20'],

            'address_line1' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address_line2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'ward' => ['sometimes', 'nullable', 'string', 'max:255'],
            'district' => ['sometimes', 'nullable', 'string', 'max:255'],
            'province' => ['sometimes', 'nullable', 'string', 'max:255'],
            'country' => ['sometimes', 'nullable', 'string', 'size:2'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],

            'company' => ['sometimes', 'nullable', 'string', 'max:255'],
            'job_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'timezone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'locale' => ['sometimes', 'nullable', 'string', 'max:10'],
            'bio' => ['sometimes', 'nullable', 'string'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Tên hiển thị.',
                'example' => 'Nguyễn Văn A',
            ],
            'email' => [
                'description' => 'Email (duy nhất).',
                'example' => 'demo@example.com',
            ],
            'user_type' => [
                'description' => 'Loại tài khoản (ADMIN|USER|SYSTEM).',
                'example' => 'USER',
            ],
            'phone' => [
                'description' => 'Số điện thoại.',
                'example' => '0900000000',
            ],
            'bio' => [
                'description' => 'Giới thiệu ngắn.',
                'example' => 'Mô tả ngắn về user',
            ],
        ];
    }
}

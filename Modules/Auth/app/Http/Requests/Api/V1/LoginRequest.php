<?php

namespace Modules\Auth\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'Registered email address.',
                'example' => 'demo@example.com',
            ],
            'password' => [
                'description' => 'Account password.',
                'example' => 'password123',
            ],
            'device_name' => [
                'description' => 'Optional device name for the token.',
                'example' => 'postman',
            ],
        ];
    }
}

<?php

namespace Modules\Auth\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'User display name.',
                'example' => 'Demo User',
            ],
            'email' => [
                'description' => 'Unique email address.',
                'example' => 'demo@example.com',
            ],
            'password' => [
                'description' => 'Password (min 8 chars).',
                'example' => 'password123',
            ],
            'password_confirmation' => [
                'description' => 'Must match password.',
                'example' => 'password123',
            ],
            'device_name' => [
                'description' => 'Optional device name for the token.',
                'example' => 'postman',
            ],
        ];
    }
}

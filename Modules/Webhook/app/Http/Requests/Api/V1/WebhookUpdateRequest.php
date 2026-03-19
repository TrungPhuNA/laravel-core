<?php

namespace Modules\Webhook\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

final class WebhookUpdateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'is_active' => ['sometimes', 'boolean'],
            'allowed_methods' => ['sometimes', 'array'],
            'allowed_methods.*' => ['string', Rule::in(['GET', 'POST', 'get', 'post'])],
            'auth_type' => ['sometimes', 'string', Rule::in(['none', 'token', 'hmac'])],
            'rotate_token' => ['sometimes', 'boolean'],
            'rotate_secret' => ['sometimes', 'boolean'],
            'validation_rules' => ['sometimes', 'nullable', 'array'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Tên webhook.',
                'example' => 'Webhook Payment Provider',
            ],
            'is_active' => [
                'description' => 'Bật/tắt webhook.',
                'example' => true,
            ],
            'allowed_methods' => [
                'description' => 'Danh sách method cho phép: GET/POST.',
                'example' => ['GET', 'POST'],
            ],
            'auth_type' => [
                'description' => 'Cấu hình auth: none|token|hmac.',
                'example' => 'hmac',
            ],
            'rotate_token' => [
                'description' => 'Nếu auth_type=token, set true để tạo token mới.',
                'example' => true,
            ],
            'rotate_secret' => [
                'description' => 'Nếu auth_type=hmac, set true để tạo secret mới.',
                'example' => true,
            ],
            'validation_rules' => [
                'description' => 'Laravel validation rules cho payload nhận vào.',
                'example' => [
                    'order_id' => 'required|string|max:50',
                ],
            ],
            'description' => [
                'description' => 'Mô tả ngắn.',
                'example' => 'Nhận callback thanh toán',
            ],
        ];
    }
}

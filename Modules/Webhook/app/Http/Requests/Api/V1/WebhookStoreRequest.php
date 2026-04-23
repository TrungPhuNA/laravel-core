<?php

namespace Modules\Webhook\Http\Requests\Api\V1;

use Illuminate\Validation\Rule;
use App\Core\Http\Requests\ApiFormRequest;

final class WebhookStoreRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'type' => ['sometimes', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
            'allowed_methods' => ['sometimes', 'array'],
            'allowed_methods.*' => ['string', Rule::in(['GET', 'POST', 'get', 'post'])],
            'auth_type' => ['sometimes', 'string', Rule::in(['none', 'token', 'hmac'])],
            'validation_rules' => ['sometimes', 'nullable', 'array'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Tên webhook (dùng để quản lý).',
                'example' => 'Webhook Payment Provider',
            ],
            'is_active' => [
                'description' => 'Bật/tắt webhook.',
                'example' => true,
            ],
            'allowed_methods' => [
                'description' => 'Danh sách method cho phép: GET/POST. Nếu bỏ trống: mặc định cho cả GET và POST.',
                'example' => ['POST'],
            ],
            'auth_type' => [
                'description' => 'Cấu hình auth: none|token|hmac.',
                'example' => 'hmac',
            ],
            'validation_rules' => [
                'description' => 'Laravel validation rules cho payload nhận vào (key => rules).',
                'example' => [
                    'email' => 'required|email',
                    'amount' => 'nullable|numeric',
                ],
            ],
            'description' => [
                'description' => 'Mô tả ngắn.',
                'example' => 'Nhận callback thanh toán',
            ],
        ];
    }
}

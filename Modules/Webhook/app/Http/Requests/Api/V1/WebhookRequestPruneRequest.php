<?php

namespace Modules\Webhook\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class WebhookRequestPruneRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            // Either pass "before" (datetime/date) or "days" (int).
            'before' => ['sometimes', 'date'],
            'days' => ['sometimes', 'integer', 'min:0', 'max:3650'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'before' => [
                'description' => 'Xoá log có received_at < before (datetime).',
                'example' => '2026-03-01 00:00:00',
            ],
            'days' => [
                'description' => 'Xoá log cũ hơn N ngày (tính từ hiện tại). Nếu truyền thì ưu tiên dùng days.',
                'example' => 30,
            ],
        ];
    }
}


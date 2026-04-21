<?php

namespace Modules\Webhook\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class WebhookDestinationUpdateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'url' => ['sometimes', 'string', 'max:2048', 'url'],
            'http_method' => ['sometimes', 'string', 'in:GET,POST,PUT,PATCH,DELETE'],
            'is_active' => ['sometimes', 'boolean'],
            'headers' => ['sometimes', 'nullable', 'array'],
            'headers.*' => ['nullable'],
            'send_mode' => ['sometimes', 'string', 'in:merge,mapped_only'],
            'field_mappings' => ['sometimes', 'nullable', 'array'],
            'field_mappings.*.from' => ['required_with:field_mappings', 'string', 'max:150'],
            'field_mappings.*.to' => ['required_with:field_mappings', 'string', 'max:150'],
            'drop_mapped_sources' => ['sometimes', 'boolean'],
            'timeout_seconds' => ['sometimes', 'integer', 'min:1', 'max:60'],
        ];
    }
}


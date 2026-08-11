<?php

namespace Modules\Monitor\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class DomainBulkStoreRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'domains' => ['required', 'array', 'max:500'],
            'domains.*' => ['required', 'string', 'max:500'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'domains' => [
                'description' => 'Danh sách domain (dán mỗi dòng 1 domain, có thể kèm https://).',
                'example' => ['https://a.com/', 'b.net', 'c.vn'],
            ],
        ];
    }
}
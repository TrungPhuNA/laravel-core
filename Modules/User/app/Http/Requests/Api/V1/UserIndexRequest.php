<?php

namespace Modules\User\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class UserIndexRequest extends ApiFormRequest
{
    public function rules(): array
    {
        // Query params cho list được parse bằng ApiQueryParams + allow-list ở repository.
        return [
            'filter' => ['sometimes', 'array'],
            'include' => ['sometimes', 'string'],
            'sort' => ['sometimes', 'string'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}


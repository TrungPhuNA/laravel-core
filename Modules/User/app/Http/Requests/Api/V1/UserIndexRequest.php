<?php

namespace Modules\User\Http\Requests\Api\V1;

use App\Core\Http\Requests\ApiFormRequest;

final class UserIndexRequest extends ApiFormRequest
{
    public function rules(): array
    {
        // Query params cho list được parse bằng ApiQueryParams + allow-list ở repository.
        return [
            // Khuyến nghị dùng filters[*], vẫn hỗ trợ filter[*] để tương thích ngược.
            'filters' => ['sometimes', 'array'],
            'filter' => ['sometimes', 'array'],
            // Nếu client gửi include/sort nhưng value rỗng thì coi như không truyền.
            'include' => ['sometimes', 'nullable', 'string'],
            'sort' => ['sometimes', 'nullable', 'string'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function queryParameters(): array
    {
        return [
            'filters[id]' => [
                'description' => 'Lọc theo ID (exact).',
                'example' => 1,
            ],
            'filters[name]' => [
                'description' => 'Lọc theo tên (LIKE).',
                'example' => 'demo',
            ],
            'filters[email]' => [
                'description' => 'Lọc theo email (LIKE).',
                'example' => '@gmail.com',
            ],
            'filters[user_type]' => [
                'description' => 'Lọc theo loại tài khoản (exact).',
                'example' => 'ADMIN',
            ],
            'filters[phone]' => [
                'description' => 'Lọc theo số điện thoại (LIKE).',
                'example' => '0986',
            ],
            'filters[created_at]' => [
                'description' => 'Lọc theo khoảng thời gian tạo. Có thể truyền "from,to" hoặc dạng mảng {from,to}.',
                'example' => '2026-01-01,2026-01-31',
            ],
            'sort' => [
                'description' => 'Sắp xếp. Ví dụ: "-id" (mặc định nếu bỏ trống), "-created_at,name".',
                'example' => '-id',
            ],
            'page' => [
                'description' => 'Trang hiện tại.',
                'example' => 1,
            ],
            'per_page' => [
                'description' => 'Số item mỗi trang (giới hạn theo CORE_API_MAX_PER_PAGE).',
                'example' => 20,
            ],
        ];
    }
}

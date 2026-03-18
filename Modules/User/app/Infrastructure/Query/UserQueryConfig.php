<?php

namespace Modules\User\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

/**
 * Nơi cấu hình query cho "User list" theo từng model.
 *
 * Mục tiêu:
 * - Khi dự án lớn, allow-list filter/sort nên gom lại 1 chỗ để dễ maintain.
 * - Controller/Service chỉ gọi paginate(); repository apply theo config này.
 */
final class UserQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'id' => ApiQueryApplier::FILTER_EXACT,
            'name' => ApiQueryApplier::FILTER_LIKE,
            'email' => ApiQueryApplier::FILTER_LIKE,
            'user_type' => ApiQueryApplier::FILTER_EXACT,
            'phone' => ApiQueryApplier::FILTER_LIKE,

            // Khoảng thời gian (ví dụ):
            // filters[created_at]=2026-01-01,2026-01-31
            // hoặc filters[created_at][from]=2026-01-01&filters[created_at][to]=2026-01-31
            'created_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'name', 'email', 'user_type', 'created_at', 'updated_at'];
    }

    /**
     * Sort mặc định nếu client không truyền `sort`.
     *
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['-id'];
    }
}


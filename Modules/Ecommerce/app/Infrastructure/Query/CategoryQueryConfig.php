<?php

namespace Modules\Ecommerce\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class CategoryQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'id' => ApiQueryApplier::FILTER_EXACT,
            'parent_id' => ApiQueryApplier::FILTER_EXACT,
            'name' => ApiQueryApplier::FILTER_LIKE,
            'slug' => ApiQueryApplier::FILTER_LIKE,
            'is_active' => ApiQueryApplier::FILTER_EXACT,
            'created_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'name', 'position', 'created_at', 'updated_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['position', 'id'];
    }
}


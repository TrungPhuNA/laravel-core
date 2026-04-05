<?php

namespace Modules\Ecommerce\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class CustomerQueryConfig
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
            'phone' => ApiQueryApplier::FILTER_LIKE,
            'created_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'name', 'email', 'created_at', 'updated_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['-id'];
    }
}


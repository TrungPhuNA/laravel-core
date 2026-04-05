<?php

namespace Modules\Ecommerce\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class ProductQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'id' => ApiQueryApplier::FILTER_EXACT,
            'sku' => ApiQueryApplier::FILTER_LIKE,
            'name' => ApiQueryApplier::FILTER_LIKE,
            'slug' => ApiQueryApplier::FILTER_LIKE,
            'currency' => ApiQueryApplier::FILTER_EXACT,
            'is_active' => ApiQueryApplier::FILTER_EXACT,
            'created_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'name', 'price', 'stock_qty', 'created_at', 'updated_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['-id'];
    }
}


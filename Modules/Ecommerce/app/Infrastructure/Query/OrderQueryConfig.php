<?php

namespace Modules\Ecommerce\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class OrderQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'id' => ApiQueryApplier::FILTER_EXACT,
            'code' => ApiQueryApplier::FILTER_LIKE,
            'status' => ApiQueryApplier::FILTER_EXACT,
            'payment_status' => ApiQueryApplier::FILTER_EXACT,
            'fulfillment_status' => ApiQueryApplier::FILTER_EXACT,
            'customer_email' => ApiQueryApplier::FILTER_LIKE,
            'customer_phone' => ApiQueryApplier::FILTER_LIKE,
            'placed_at' => ApiQueryApplier::FILTER_RANGE,
            'created_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'total', 'placed_at', 'created_at', 'updated_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['-id'];
    }
}


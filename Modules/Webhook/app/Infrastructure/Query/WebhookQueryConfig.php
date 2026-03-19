<?php

namespace Modules\Webhook\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class WebhookQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'name' => ApiQueryApplier::FILTER_LIKE,
            'is_active' => ApiQueryApplier::FILTER_EXACT,
            'auth_type' => ApiQueryApplier::FILTER_EXACT,
            'created_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'name', 'is_active', 'auth_type', 'created_at', 'updated_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['-id'];
    }
}


<?php

namespace Modules\Monitor\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class DomainQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'domain' => ApiQueryApplier::FILTER_LIKE,
            'is_active' => ApiQueryApplier::FILTER_EXACT,
            'check_status' => ApiQueryApplier::FILTER_EXACT,
            'expires_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'domain', 'expires_at', 'last_check_at', 'created_at', 'updated_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['expires_at'];
    }
}
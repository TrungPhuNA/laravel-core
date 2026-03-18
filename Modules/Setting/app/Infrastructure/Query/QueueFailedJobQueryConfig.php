<?php

namespace Modules\Setting\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class QueueFailedJobQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'queue' => ApiQueryApplier::FILTER_EXACT,
            'connection' => ApiQueryApplier::FILTER_EXACT,
            'failed_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'queue', 'connection', 'failed_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['-id'];
    }
}


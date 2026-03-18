<?php

namespace Modules\Setting\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class QueueJobQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'queue' => ApiQueryApplier::FILTER_EXACT,
            'attempts' => ApiQueryApplier::FILTER_EXACT,
            'created_at' => ApiQueryApplier::FILTER_RANGE,
            'available_at' => ApiQueryApplier::FILTER_RANGE,
            'reserved_at' => ApiQueryApplier::FILTER_RANGE,
            // 'status' duoc xu ly rieng (pending|reserved|delayed|all)
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'queue', 'attempts', 'reserved_at', 'available_at', 'created_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['-id'];
    }
}


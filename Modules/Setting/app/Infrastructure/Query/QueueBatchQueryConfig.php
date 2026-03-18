<?php

namespace Modules\Setting\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class QueueBatchQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'name' => ApiQueryApplier::FILTER_LIKE,
            'created_at' => ApiQueryApplier::FILTER_RANGE,
            'finished_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'name', 'total_jobs', 'pending_jobs', 'failed_jobs', 'created_at', 'finished_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['-created_at'];
    }
}


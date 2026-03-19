<?php

namespace Modules\Webhook\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class WebhookRequestQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'method' => ApiQueryApplier::FILTER_EXACT,
            'ip' => ApiQueryApplier::FILTER_EXACT,
            'received_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'method', 'received_at', 'created_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['-received_at'];
    }
}


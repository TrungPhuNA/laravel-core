<?php

namespace Modules\Webhook\Infrastructure\Query;

final class WebhookDispatchLogQueryConfig
{
    public static function allowedFilters(): array
    {
        return [
            'id' => ['type' => 'exact', 'column' => 'id'],
            'webhook_request_id' => ['type' => 'exact', 'column' => 'webhook_request_id'],
            'status' => ['type' => 'exact', 'column' => 'status'],
            'destination_id' => ['type' => 'exact', 'column' => 'destination_id'],
            'response_status' => ['type' => 'exact', 'column' => 'response_status'],
            'created_at' => ['type' => 'date_range', 'column' => 'created_at'],
        ];
    }

    public static function allowedSorts(): array
    {
        return ['id', 'created_at', 'duration_ms', 'response_status'];
    }

    public static function defaultSorts(): array
    {
        return ['-id'];
    }
}


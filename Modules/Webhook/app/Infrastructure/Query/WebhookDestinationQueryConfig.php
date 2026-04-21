<?php

namespace Modules\Webhook\Infrastructure\Query;

final class WebhookDestinationQueryConfig
{
    public static function allowedFilters(): array
    {
        return [
            'name' => ['type' => 'like', 'column' => 'name'],
            'is_active' => ['type' => 'exact', 'column' => 'is_active'],
            'http_method' => ['type' => 'exact', 'column' => 'http_method'],
            'send_mode' => ['type' => 'exact', 'column' => 'send_mode'],
            'created_at' => ['type' => 'date_range', 'column' => 'created_at'],
        ];
    }

    public static function allowedSorts(): array
    {
        return ['id', 'name', 'created_at', 'updated_at'];
    }

    public static function defaultSorts(): array
    {
        return ['-id'];
    }
}


<?php

namespace Modules\CheatSheet\Infrastructure\Query;

use App\Core\Infrastructure\Query\ApiQueryApplier;

final class CheatSheetQueryConfig
{
    /**
     * @return array<string, string>
     */
    public static function allowedFilters(): array
    {
        return [
            'title' => ApiQueryApplier::FILTER_LIKE,
            'visibility' => ApiQueryApplier::FILTER_EXACT,
            'created_at' => ApiQueryApplier::FILTER_RANGE,
            'updated_at' => ApiQueryApplier::FILTER_RANGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function allowedSorts(): array
    {
        return ['id', 'title', 'created_at', 'updated_at', 'published_at'];
    }

    /**
     * @return list<string>
     */
    public static function defaultSorts(): array
    {
        return ['-updated_at', '-id'];
    }
}


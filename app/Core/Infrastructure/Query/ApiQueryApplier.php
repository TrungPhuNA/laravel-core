<?php

namespace App\Core\Infrastructure\Query;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Database\Eloquent\Builder;

/**
 * Áp dụng filter/sort/include theo query params cho Eloquent Builder.
 *
 * Lưu ý:
 * - Chỉ apply những field nằm trong allow-list để tránh lộ dữ liệu hoặc SQL injection qua tên cột.
 * - Mặc định ignore các filter/sort/include không được phép.
 */
final class ApiQueryApplier
{
    public const FILTER_EXACT = 'exact';
    public const FILTER_LIKE = 'like';
    public const FILTER_IN = 'in';
    public const FILTER_RANGE = 'range';

    /**
     * @param array<string, string> $allowedFilters Map: field => type (exact|like|in)
     * @param list<string> $allowedSorts
     * @param list<string> $allowedIncludes
     * @param list<string> $defaultSorts Ví dụ: ['-id'] hoặc ['-created_at', 'id']
     */
    public static function apply(
        Builder $query,
        ApiQueryParams $params,
        array $allowedFilters = [],
        array $allowedSorts = [],
        array $allowedIncludes = [],
        array $defaultSorts = [],
    ): Builder {
        self::applyIncludes($query, $params, $allowedIncludes);
        self::applyFilters($query, $params, $allowedFilters);
        self::applySorts($query, $params, $allowedSorts, $defaultSorts);

        return $query;
    }

    /**
     * @param list<string> $allowedIncludes
     */
    private static function applyIncludes(Builder $query, ApiQueryParams $params, array $allowedIncludes): void
    {
        if ($allowedIncludes === []) {
            return;
        }

        $wanted = [];
        foreach ($params->includes as $include) {
            if (!self::isSafeRelation($include)) {
                continue;
            }
            if (in_array($include, $allowedIncludes, true)) {
                $wanted[] = $include;
            }
        }

        if ($wanted !== []) {
            $query->with($wanted);
        }
    }

    /**
     * @param array<string, string> $allowedFilters
     */
    private static function applyFilters(Builder $query, ApiQueryParams $params, array $allowedFilters): void
    {
        if ($allowedFilters === []) {
            return;
        }

        foreach ($params->filters as $field => $value) {
            $field = (string) $field;
            if (!array_key_exists($field, $allowedFilters)) {
                continue;
            }
            if (!self::isSafeColumn($field)) {
                continue;
            }

            $type = $allowedFilters[$field];

            if ($type === self::FILTER_EXACT) {
                if ($value === null || $value === '') {
                    continue;
                }
                $query->where($field, $value);
                continue;
            }

            if ($type === self::FILTER_LIKE) {
                if (!is_string($value)) {
                    continue;
                }
                $value = trim($value);
                if ($value === '') {
                    continue;
                }
                $query->where($field, 'like', '%'.$value.'%');
                continue;
            }

            if ($type === self::FILTER_IN) {
                $values = self::normalizeList($value);
                if ($values === []) {
                    continue;
                }
                $query->whereIn($field, $values);
                continue;
            }

            if ($type === self::FILTER_RANGE) {
                [$from, $to] = self::normalizeRange($value);

                if ($from !== null) {
                    $query->where($field, '>=', $from);
                }

                if ($to !== null) {
                    $query->where($field, '<=', $to);
                }

                continue;
            }
        }
    }

    /**
     * @param list<string> $allowedSorts
     * @param list<string> $defaultSorts
     */
    private static function applySorts(Builder $query, ApiQueryParams $params, array $allowedSorts, array $defaultSorts): void
    {
        if ($allowedSorts === []) {
            return;
        }

        $sorts = $params->sorts !== [] ? $params->sorts : $defaultSorts;

        foreach ($sorts as $sort) {
            $sort = trim($sort);
            if ($sort === '') {
                continue;
            }

            $direction = 'asc';
            $field = $sort;
            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $field = substr($sort, 1);
            }

            if ($field === '' || !in_array($field, $allowedSorts, true)) {
                continue;
            }
            if (!self::isSafeColumn($field)) {
                continue;
            }

            $query->orderBy($field, $direction);
        }
    }

    private static function isSafeColumn(string $value): bool
    {
        // Cho phép qualified column: users.email
        return (bool) preg_match('/^[A-Za-z0-9_\\.]+$/', $value);
    }

    private static function isSafeRelation(string $value): bool
    {
        // Cho phep nested relation: category.parent
        return (bool) preg_match('/^[A-Za-z0-9_\\.]+$/', $value);
    }

    /**
     * @return list<mixed>
     */
    private static function normalizeList(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value, static fn ($v) => $v !== null && $v !== ''));
        }

        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return [];
            }
            $parts = array_map('trim', explode(',', $value));
            return array_values(array_filter($parts, static fn ($v) => $v !== ''));
        }

        return [];
    }

    /**
     * @return array{0: string|null, 1: string|null} [from, to]
     */
    private static function normalizeRange(mixed $value): array
    {
        // Accept array: ['from' => '2026-01-01', 'to' => '2026-01-31']
        if (is_array($value)) {
            $from = $value['from'] ?? $value['gte'] ?? $value['min'] ?? null;
            $to = $value['to'] ?? $value['lte'] ?? $value['max'] ?? null;

            $from = is_string($from) ? trim($from) : null;
            $to = is_string($to) ? trim($to) : null;

            return [$from !== '' ? $from : null, $to !== '' ? $to : null];
        }

        // Accept string: "from,to"
        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                return [null, null];
            }

            [$from, $to] = array_pad(array_map('trim', explode(',', $value, 2)), 2, null);

            $from = is_string($from) ? trim($from) : null;
            $to = is_string($to) ? trim($to) : null;

            return [$from !== '' ? $from : null, $to !== '' ? $to : null];
        }

        return [null, null];
    }
}

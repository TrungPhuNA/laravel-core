<?php

namespace App\Core\Support\Query;

use Illuminate\Http\Request;

/**
 * Parse query params theo convention chung cho API list endpoint.
 *
 * Convention:
 * - filters[field]=value (khuyến nghị)
 * - filter[field]=value (tương thích ngược)
 * - sort=field,-created_at
 * - include=category,brand
 * - page=1
 * - per_page=20
 */
final class ApiQueryParams
{
    /**
     * @param array<string, mixed> $filters
     * @param list<string> $includes
     * @param list<string> $sorts Raw sorts, ví dụ: ["name", "-created_at"]
     */
    public function __construct(
        public readonly array $filters,
        public readonly array $includes,
        public readonly array $sorts,
        public readonly int $page,
        public readonly int $perPage,
    ) {}

    public static function fromRequest(Request $request): self
    {
        /** @var array<string, mixed> $filters */
        $filters = $request->input('filters', $request->input('filter', []));
        if (!is_array($filters)) {
            $filters = [];
        }
        $filters = self::sanitizeFilters($filters);

        $includes = self::csv($request->input('include'));
        $sorts = self::csv($request->input('sort'));

        $pageParam = (string) config('core.api.pagination.page_param', 'page');
        $perPageParam = (string) config('core.api.pagination.per_page_param', 'per_page');

        $page = (int) $request->input($pageParam, 1);
        $perPage = (int) $request->input($perPageParam, (int) config('core.api.pagination.default_per_page', 20));

        $page = max(1, $page);

        $maxPerPage = (int) config('core.api.pagination.max_per_page', 100);
        $perPage = max(1, min($perPage, $maxPerPage));

        return new self(
            filters: $filters,
            includes: $includes,
            sorts: $sorts,
            page: $page,
            perPage: $perPage,
        );
    }

    /**
     * Xoá các filter có value rỗng/null/"null"/"undefined" để tránh phải check ở từng module.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    private static function sanitizeFilters(array $filters): array
    {
        $out = [];

        foreach ($filters as $key => $value) {
            $key = (string) $key;

            if (is_array($value)) {
                /** @var array<string, mixed> $value */
                $child = self::sanitizeFilters($value);
                if ($child !== []) {
                    $out[$key] = $child;
                }
                continue;
            }

            if ($value === null) {
                continue;
            }

            if (is_string($value)) {
                $v = trim($value);
                if ($v === '') {
                    continue;
                }
                $lower = strtolower($v);
                if ($lower === 'null' || $lower === 'undefined') {
                    continue;
                }
                $out[$key] = $v;
                continue;
            }

            $out[$key] = $value;
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private static function csv(mixed $value): array
    {
        if (!is_string($value)) {
            return [];
        }

        $value = trim($value);
        if ($value === '') {
            return [];
        }

        $parts = array_map('trim', explode(',', $value));
        $parts = array_values(array_filter($parts, static fn ($p) => $p !== ''));

        /** @var list<string> $parts */
        return $parts;
    }
}

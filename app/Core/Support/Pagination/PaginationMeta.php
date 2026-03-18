<?php

namespace App\Core\Support\Pagination;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class PaginationMeta
{
    /**
     * Chuẩn meta pagination dùng chung cho API list.
     *
     * @return array{pagination: array{page:int, per_page:int, total:int, last_page:int}}
     */
    public static function fromLengthAwarePaginator(LengthAwarePaginator $paginator): array
    {
        return [
            'pagination' => [
                'page' => (int) $paginator->currentPage(),
                'per_page' => (int) $paginator->perPage(),
                'total' => (int) $paginator->total(),
                'last_page' => (int) $paginator->lastPage(),
            ],
        ];
    }
}


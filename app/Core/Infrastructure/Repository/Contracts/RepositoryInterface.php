<?php

namespace App\Core\Infrastructure\Repository\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Repository interface tối thiểu.
 *
 * Mục tiêu:
 * - Giúp thay implementation (Eloquent, API client, raw SQL) mà không đổi service.
 * - Dễ fake/mock khi test.
 */
interface RepositoryInterface
{
    public function find(mixed $id): ?Model;

    public function findOrFail(mixed $id): Model;
}


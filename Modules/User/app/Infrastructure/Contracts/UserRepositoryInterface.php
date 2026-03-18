<?php

namespace Modules\User\Infrastructure\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function findOrFail(int $id): User;

    public function findWithTrashedOrFail(int $id): User;

    /**
     * @param array<string, mixed> $input
     */
    public function create(array $input): User;

    /**
     * @param array<string, mixed> $input
     */
    public function update(User $user, array $input): User;

    public function delete(User $user): void;

    public function restore(User $user): User;
}


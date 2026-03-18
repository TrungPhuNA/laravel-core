<?php

namespace Modules\User\Application\Contracts;

use App\Core\Support\Query\ApiQueryParams;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserServiceInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator;

    public function getById(int $id): User;

    /**
     * @param array<string, mixed> $input
     */
    public function create(array $input): User;

    /**
     * @param array<string, mixed> $input
     */
    public function update(int $id, array $input): User;

    public function updateUserType(int $id, string $userType): User;

    public function resetPassword(int $id, string $password): User;

    public function delete(int $id): void;

    public function restore(int $id): User;
}


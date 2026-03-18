<?php

namespace Modules\User\Infrastructure\Repositories;

use App\Core\Infrastructure\Query\ApiQueryApplier;
use App\Core\Support\Query\ApiQueryParams;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Infrastructure\Contracts\UserRepositoryInterface;
use Modules\User\Infrastructure\Query\UserQueryConfig;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        $query = User::query();

        ApiQueryApplier::apply(
            query: $query,
            params: $params,
            allowedFilters: UserQueryConfig::allowedFilters(),
            allowedSorts: UserQueryConfig::allowedSorts(),
            allowedIncludes: [],
            defaultSorts: UserQueryConfig::defaultSorts(),
        );

        return $query->paginate(
            perPage: $params->perPage,
            page: $params->page,
        );
    }

    public function findOrFail(int $id): User
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);

        return $user;
    }

    public function findWithTrashedOrFail(int $id): User
    {
        /** @var User $user */
        $user = User::withTrashed()->findOrFail($id);

        return $user;
    }

    public function create(array $input): User
    {
        /** @var User $user */
        $user = User::create($input);

        return $user->refresh();
    }

    public function update(User $user, array $input): User
    {
        $user->fill($input);
        $user->save();

        return $user->refresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function restore(User $user): User
    {
        $user->restore();

        return $user->refresh();
    }
}

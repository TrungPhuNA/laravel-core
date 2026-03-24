<?php

namespace Modules\Setting\Application\Services;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RbacService implements \Modules\Setting\Application\Contracts\RbacServiceInterface
{
    private string $guard;

    public function __construct()
    {
        $this->guard = (string) config('core.rbac.guard', 'sanctum');
    }

    public function listRoles(): Collection
    {
        return Role::query()
            ->where('guard_name', $this->guard)
            ->with('permissions')
            ->orderBy('name')
            ->get();
    }

    public function getRoleById(int $id): Role
    {
        /** @var Role|null $role */
        $role = Role::query()
            ->where('guard_name', $this->guard)
            ->with('permissions')
            ->find($id);

        if (!$role) {
            throw new ApiException(
                errorCode: ErrorCode::NOT_FOUND->value,
                message: __('messages.not_found'),
                status: 404,
            );
        }

        return $role;
    }

    public function createRole(string $name, array $permissionNames = []): Role
    {
        $name = trim($name);
        if ($name === '') {
            throw ApiException::unprocessable(
                ErrorCode::VALIDATION_ERROR->value,
                __('messages.validation_error'),
                ['name' => ['The name field is required.']],
            );
        }

        return DB::transaction(function () use ($name, $permissionNames) {
            $role = Role::query()->create([
                'name' => $name,
                'guard_name' => $this->guard,
            ]);

            if ($permissionNames !== []) {
                $permissions = Permission::query()
                    ->where('guard_name', $this->guard)
                    ->whereIn('name', $permissionNames)
                    ->get();

                $role->syncPermissions($permissions);
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $role->load('permissions');
        });
    }

    public function updateRole(int $id, array $input): Role
    {
        $role = $this->getRoleById($id);

        return DB::transaction(function () use ($role, $input) {
            if (array_key_exists('name', $input) && is_string($input['name'])) {
                $name = trim($input['name']);
                if ($name === '') {
                    throw ApiException::unprocessable(
                        ErrorCode::VALIDATION_ERROR->value,
                        __('messages.validation_error'),
                        ['name' => ['The name field is required.']],
                    );
                }
                $role->name = $name;
                $role->save();
            }

            if (array_key_exists('permissions', $input) && is_array($input['permissions'])) {
                /** @var list<string> $permissionNames */
                $permissionNames = array_values(array_filter(array_map('strval', $input['permissions']), static fn ($v) => trim($v) !== ''));

                $permissions = Permission::query()
                    ->where('guard_name', $this->guard)
                    ->whereIn('name', $permissionNames)
                    ->get();

                $role->syncPermissions($permissions);
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $role->load('permissions');
        });
    }

    public function deleteRole(int $id): void
    {
        $role = $this->getRoleById($id);

        DB::transaction(function () use ($role) {
            $role->delete();
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        });
    }

    public function listPermissions(): Collection
    {
        return Permission::query()
            ->where('guard_name', $this->guard)
            ->orderBy('name')
            ->get();
    }

    public function createPermission(string $name): Permission
    {
        $name = trim($name);
        if ($name === '') {
            throw ApiException::unprocessable(
                ErrorCode::VALIDATION_ERROR->value,
                __('messages.validation_error'),
                ['name' => ['The name field is required.']],
            );
        }

        return DB::transaction(function () use ($name) {
            $perm = Permission::findOrCreate($name, $this->guard);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            return $perm;
        });
    }

    public function getUserById(int $id): User
    {
        /** @var User|null $user */
        $user = User::query()->with(['roles', 'permissions'])->find($id);

        if (!$user) {
            throw new ApiException(
                errorCode: ErrorCode::NOT_FOUND->value,
                message: __('messages.not_found'),
                status: 404,
            );
        }

        return $user;
    }

    public function syncUserRoles(int $userId, array $roleNames): User
    {
        $user = $this->getUserById($userId);

        /** @var list<string> $names */
        $names = array_values(array_filter(array_map('strval', $roleNames), static fn ($v) => trim($v) !== ''));

        return DB::transaction(function () use ($user, $names) {
            $roles = Role::query()
                ->where('guard_name', $this->guard)
                ->whereIn('name', $names)
                ->get();

            $user->syncRoles($roles);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $user->load(['roles', 'permissions']);
        });
    }

    public function syncUserPermissions(int $userId, array $permissionNames): User
    {
        $user = $this->getUserById($userId);

        /** @var list<string> $names */
        $names = array_values(array_filter(array_map('strval', $permissionNames), static fn ($v) => trim($v) !== ''));

        return DB::transaction(function () use ($user, $names) {
            $permissions = Permission::query()
                ->where('guard_name', $this->guard)
                ->whereIn('name', $names)
                ->get();

            $user->syncPermissions($permissions);
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $user->load(['roles', 'permissions']);
        });
    }
}


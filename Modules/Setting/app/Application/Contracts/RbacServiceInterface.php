<?php

namespace Modules\Setting\Application\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

interface RbacServiceInterface
{
    /**
     * @return Collection<int, Role>
     */
    public function listRoles(): Collection;

    public function getRoleById(int $id): Role;

    /**
     * @param list<string> $permissionNames
     */
    public function createRole(string $name, array $permissionNames = []): Role;

    /**
     * @param array{name?:string, permissions?:list<string>} $input
     */
    public function updateRole(int $id, array $input): Role;

    public function deleteRole(int $id): void;

    /**
     * @return Collection<int, Permission>
     */
    public function listPermissions(): Collection;

    public function createPermission(string $name): Permission;

    public function getUserById(int $id): User;

    /**
     * @param list<string> $roleNames
     */
    public function syncUserRoles(int $userId, array $roleNames): User;

    /**
     * @param list<string> $permissionNames
     */
    public function syncUserPermissions(int $userId, array $permissionNames): User;
}


<?php

namespace Modules\Setting\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $guard = (string) config('core.rbac.guard', 'sanctum');

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'setting.users.read',
            'setting.users.write',
            'setting.users.delete',

            'setting.roles.read',
            'setting.roles.write',
            'setting.roles.delete',

            'setting.permissions.read',
            'setting.permissions.write',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, $guard);
        }

        $adminRole = Role::findOrCreate('Admin', $guard);
        $adminRole->syncPermissions($permissions);

        $superAdminEmails = (array) config('core.rbac.super_admin_emails', []);
        if ($superAdminEmails !== []) {
            $users = User::query()
                ->whereIn('email', $superAdminEmails)
                ->get();

            foreach ($users as $u) {
                $u->assignRole($adminRole);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}


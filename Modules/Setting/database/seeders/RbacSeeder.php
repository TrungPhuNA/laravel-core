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

        // Tránh case Spatie cache/registrar chưa refresh kịp sau findOrCreate()
        // dẫn tới syncPermissions() không tìm thấy permission theo name+guard.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminRole = Role::findOrCreate('Admin', $guard);
        $permissionModels = Permission::query()
            ->where('guard_name', $guard)
            ->whereIn('name', $permissions)
            ->get();

        $adminRole->syncPermissions($permissionModels);

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

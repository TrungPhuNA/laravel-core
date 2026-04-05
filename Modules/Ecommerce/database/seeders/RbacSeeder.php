<?php

namespace Modules\Ecommerce\Database\Seeders;

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
            'ecommerce.dashboard.read',

            'ecommerce.shops.read',
            'ecommerce.shops.write',

            'ecommerce.categories.read',
            'ecommerce.categories.write',
            'ecommerce.categories.delete',

            'ecommerce.products.read',
            'ecommerce.products.write',
            'ecommerce.products.delete',

            'ecommerce.customers.read',
            'ecommerce.customers.write',
            'ecommerce.customers.delete',

            'ecommerce.orders.read',
            'ecommerce.orders.write',
            'ecommerce.orders.delete',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, $guard);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Gán thêm quyền mới cho role Admin (không sync để tránh ghi đè quyền từ module khác).
        $adminRole = Role::findOrCreate('Admin', $guard);
        $permissionModels = Permission::query()
            ->where('guard_name', $guard)
            ->whereIn('name', $permissions)
            ->get();

        $adminRole->givePermissionTo($permissionModels);

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

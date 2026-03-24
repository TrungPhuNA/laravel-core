<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Support upgrade path: nếu project đã migrate theo default table names của Spatie
        // thì đổi tên sang prefix `alc_`.
        $map = [
            'permissions' => 'alc_permissions',
            'roles' => 'alc_roles',
            'model_has_permissions' => 'alc_model_has_permissions',
            'model_has_roles' => 'alc_model_has_roles',
            'role_has_permissions' => 'alc_role_has_permissions',
        ];

        foreach ($map as $from => $to) {
            if (Schema::hasTable($from) && !Schema::hasTable($to)) {
                Schema::rename($from, $to);
            }
        }
    }

    public function down(): void
    {
        $map = [
            'alc_permissions' => 'permissions',
            'alc_roles' => 'roles',
            'alc_model_has_permissions' => 'model_has_permissions',
            'alc_model_has_roles' => 'model_has_roles',
            'alc_role_has_permissions' => 'role_has_permissions',
        ];

        foreach ($map as $from => $to) {
            if (Schema::hasTable($from) && !Schema::hasTable($to)) {
                Schema::rename($from, $to);
            }
        }
    }
};


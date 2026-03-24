<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Spatie Laravel Permission
    |--------------------------------------------------------------------------
    |
    | Custom config cho `spatie/laravel-permission`.
    | Yêu cầu: các bảng RBAC có prefix `alc_`.
    |
    */

    'models' => [
        'permission' => Spatie\Permission\Models\Permission::class,
        'role' => Spatie\Permission\Models\Role::class,
    ],

    'table_names' => [
        'roles' => 'alc_roles',
        'permissions' => 'alc_permissions',
        'model_has_permissions' => 'alc_model_has_permissions',
        'model_has_roles' => 'alc_model_has_roles',
        'role_has_permissions' => 'alc_role_has_permissions',
    ],

    'column_names' => [
        'role_pivot_key' => null,
        'permission_pivot_key' => null,
        'model_morph_key' => 'model_id',
        'team_foreign_key' => 'team_id',
    ],

    'register_permission_check_method' => true,

    'teams' => false,
    'team_resolver' => Spatie\Permission\DefaultTeamResolver::class,

    'use_passport_client_credentials' => false,

    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,

    'enable_wildcard_permission' => false,

    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'spatie.permission.cache',
        'store' => 'default',
    ],
];


<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\Api\V1\SettingController;
use Modules\Setting\Http\Controllers\Api\V1\Queue\QueueBatchController;
use Modules\Setting\Http\Controllers\Api\V1\Queue\QueueFailedJobController;
use Modules\Setting\Http\Controllers\Api\V1\Queue\QueueJobController;
use Modules\Setting\Http\Controllers\Api\V1\Queue\QueueStatsController;
use Modules\Setting\Http\Controllers\Api\V1\Rbac\PermissionController;
use Modules\Setting\Http\Controllers\Api\V1\Rbac\RoleController;
use Modules\Setting\Http\Controllers\Api\V1\Rbac\UserRbacController;

Route::prefix('v1/settings')->group(function () {
    Route::get('public', [SettingController::class, 'public']);

    Route::middleware(['auth:sanctum', 'user_type:ADMIN,SYSTEM'])->group(function () {
        Route::get('/', [SettingController::class, 'index']);
        Route::put('/', [SettingController::class, 'upsert']);

        // RBAC (role/permission + gan cho user)
        Route::prefix('rbac')->group(function () {
            Route::get('roles', [RoleController::class, 'index'])->middleware('perm:setting.roles.read');
            Route::post('roles', [RoleController::class, 'store'])->middleware('perm:setting.roles.write');
            Route::get('roles/{id}', [RoleController::class, 'show'])->whereNumber('id')->middleware('perm:setting.roles.read');
            Route::put('roles/{id}', [RoleController::class, 'update'])->whereNumber('id')->middleware('perm:setting.roles.write');
            Route::delete('roles/{id}', [RoleController::class, 'destroy'])->whereNumber('id')->middleware('perm:setting.roles.delete');

            Route::get('permissions', [PermissionController::class, 'index'])->middleware('perm:setting.permissions.read');
            Route::post('permissions', [PermissionController::class, 'store'])->middleware('perm:setting.permissions.write');

            Route::get('users/{id}', [UserRbacController::class, 'show'])->whereNumber('id')->middleware('perm:setting.users.read');
            Route::put('users/{id}/roles', [UserRbacController::class, 'syncRoles'])->whereNumber('id')->middleware('perm:setting.users.write');
            Route::put('users/{id}/permissions', [UserRbacController::class, 'syncPermissions'])->whereNumber('id')->middleware('perm:setting.users.write');
        });

        // Quan ly queue (jobs/failed_jobs/job_batches) de debug/van hanh.
        Route::prefix('queue')->group(function () {
            Route::get('stats', [QueueStatsController::class, 'show']);

            Route::get('jobs', [QueueJobController::class, 'index']);
            Route::get('jobs/{id}', [QueueJobController::class, 'show'])->whereNumber('id');

            Route::get('failed-jobs', [QueueFailedJobController::class, 'index']);
            Route::get('failed-jobs/{id}', [QueueFailedJobController::class, 'show'])->whereNumber('id');
            Route::post('failed-jobs/{id}/retry', [QueueFailedJobController::class, 'retry'])->whereNumber('id');
            Route::delete('failed-jobs/{id}', [QueueFailedJobController::class, 'forget'])->whereNumber('id');

            Route::get('batches', [QueueBatchController::class, 'index']);
            Route::get('batches/{id}', [QueueBatchController::class, 'show']);
        });
    });

    // Luu y: dat route {key} cuoi cung de tranh "nuot" cac path con (vi du: queue/*).
    // Chi match 1 segment (khong chua "/") va loai tru cac keyword da dung.
    Route::get('{key}', [SettingController::class, 'show'])
        ->where('key', '^(?!public$)(?!queue$)[^/]+$');
});

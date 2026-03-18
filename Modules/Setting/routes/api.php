<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\Api\V1\SettingController;
use Modules\Setting\Http\Controllers\Api\V1\Queue\QueueBatchController;
use Modules\Setting\Http\Controllers\Api\V1\Queue\QueueFailedJobController;
use Modules\Setting\Http\Controllers\Api\V1\Queue\QueueJobController;
use Modules\Setting\Http\Controllers\Api\V1\Queue\QueueStatsController;

Route::prefix('v1/settings')->group(function () {
    Route::get('public', [SettingController::class, 'public']);

    Route::middleware(['auth:sanctum', 'user_type:ADMIN,SYSTEM'])->group(function () {
        Route::get('/', [SettingController::class, 'index']);
        Route::put('/', [SettingController::class, 'upsert']);

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

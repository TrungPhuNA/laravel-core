<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\Api\V1\SettingController;

Route::prefix('v1/settings')->group(function () {
    Route::get('public', [SettingController::class, 'public']);
    Route::get('{key}', [SettingController::class, 'show'])->where('key', '^(?!public$).+');

    Route::middleware(['auth:sanctum', 'user_type:ADMIN,SYSTEM'])->group(function () {
        Route::get('/', [SettingController::class, 'index']);
        Route::put('/', [SettingController::class, 'upsert']);
    });
});

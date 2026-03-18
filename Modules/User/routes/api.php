<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\V1\UserController;

Route::prefix('v1/users')->middleware(['auth:sanctum', 'user_type:ADMIN,SYSTEM'])->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('{id}', [UserController::class, 'show'])->whereNumber('id');

    Route::post('/', [UserController::class, 'store']);
    Route::put('{id}', [UserController::class, 'update'])->whereNumber('id');

    Route::patch('{id}/user-type', [UserController::class, 'updateUserType'])->whereNumber('id');
    Route::patch('{id}/password', [UserController::class, 'resetPassword'])->whereNumber('id');

    Route::delete('{id}', [UserController::class, 'destroy'])->whereNumber('id');
    Route::post('{id}/restore', [UserController::class, 'restore'])->whereNumber('id');
});


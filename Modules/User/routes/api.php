<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\V1\UserController;

Route::prefix('v1/users')->middleware(['auth:sanctum', 'user_type:ADMIN,SYSTEM'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->middleware('perm:setting.users.read');
    Route::get('{id}', [UserController::class, 'show'])->whereNumber('id')->middleware('perm:setting.users.read');

    Route::post('/', [UserController::class, 'store'])->middleware('perm:setting.users.write');
    Route::put('{id}', [UserController::class, 'update'])->whereNumber('id')->middleware('perm:setting.users.write');

    Route::patch('{id}/user-type', [UserController::class, 'updateUserType'])->whereNumber('id')->middleware('perm:setting.users.write');
    Route::patch('{id}/password', [UserController::class, 'resetPassword'])->whereNumber('id')->middleware('perm:setting.users.write');

    Route::delete('{id}', [UserController::class, 'destroy'])->whereNumber('id')->middleware('perm:setting.users.delete');
    Route::post('{id}/restore', [UserController::class, 'restore'])->whereNumber('id')->middleware('perm:setting.users.write');
});

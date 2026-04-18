<?php

use Illuminate\Support\Facades\Route;
use Modules\CheatSheet\Http\Controllers\Api\V1\CheatSheetController;
use Modules\CheatSheet\Http\Controllers\Api\V1\PublicCheatSheetController;

Route::prefix('v1/cheat-sheets')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [CheatSheetController::class, 'index']);
    Route::post('/', [CheatSheetController::class, 'store']);
    Route::get('topics', [CheatSheetController::class, 'topics']);
    Route::get('tags', [CheatSheetController::class, 'tags']);

    Route::get('{id}', [CheatSheetController::class, 'show'])->whereNumber('id');
    Route::put('{id}', [CheatSheetController::class, 'update'])->whereNumber('id');
    Route::delete('{id}', [CheatSheetController::class, 'destroy'])->whereNumber('id');
});

// Public browse (khong can Sanctum).
Route::prefix('v1/public/cheat-sheets')->group(function () {
    Route::get('/', [PublicCheatSheetController::class, 'index']);
    Route::get('topics', [PublicCheatSheetController::class, 'topics']);
    Route::get('{id}', [PublicCheatSheetController::class, 'show'])->whereNumber('id');
});

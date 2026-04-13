<?php

use Illuminate\Support\Facades\Route;
use Modules\Webhook\Http\Controllers\Api\V1\HealthController;
use Modules\Webhook\Http\Controllers\Api\V1\WebhookController;
use Modules\Webhook\Http\Controllers\Api\V1\WebhookReceiveController;
use Modules\Webhook\Http\Controllers\Api\V1\WebhookRequestController;

Route::prefix('v1/webhooks')->group(function () {
    // Health check (debug nhanh module da load).
    Route::get('health', [HealthController::class, 'show']);

    // Receiver public: nhan request tu ben ngoai.
    Route::match(['GET', 'POST'], 'receive/{publicId}', [WebhookReceiveController::class, 'handle'])
        ->whereUuid('publicId');

    // Quan ly webhook cua user (Sanctum).
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [WebhookController::class, 'index']);
        Route::post('/', [WebhookController::class, 'store']);
        Route::get('{id}', [WebhookController::class, 'show'])->whereNumber('id');
        Route::put('{id}', [WebhookController::class, 'update'])->whereNumber('id');
        Route::delete('{id}', [WebhookController::class, 'destroy'])->whereNumber('id');
        Route::post('{id}/rotate-token', [WebhookController::class, 'rotateToken'])->whereNumber('id');
        Route::post('{id}/rotate-secret', [WebhookController::class, 'rotateSecret'])->whereNumber('id');

        // Logs: list/show/prune theo webhook.
        Route::get('{id}/requests', [WebhookRequestController::class, 'index'])->whereNumber('id');
        Route::get('{id}/stats', [WebhookRequestController::class, 'stats'])->whereNumber('id');
        Route::get('{id}/requests/{requestId}', [WebhookRequestController::class, 'show'])->whereNumber('id')->whereNumber('requestId');
        Route::post('{id}/requests/prune', [WebhookRequestController::class, 'prune'])->whereNumber('id');
    });
});

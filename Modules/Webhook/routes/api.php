<?php

use Illuminate\Support\Facades\Route;
use Modules\Webhook\Http\Controllers\Api\V1\HealthController;
use Modules\Webhook\Http\Controllers\Api\V1\WebhookController;
use Modules\Webhook\Http\Controllers\Api\V1\WebhookDestinationController;
use Modules\Webhook\Http\Controllers\Api\V1\WebhookDispatchLogController;
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

        // Forward destinations: configure where to send.
        Route::get('{id}/destinations', [WebhookDestinationController::class, 'index'])->whereNumber('id');
        Route::post('{id}/destinations', [WebhookDestinationController::class, 'store'])->whereNumber('id');
        Route::get('{id}/destinations/{destinationId}', [WebhookDestinationController::class, 'show'])
            ->whereNumber('id')->whereNumber('destinationId');
        Route::put('{id}/destinations/{destinationId}', [WebhookDestinationController::class, 'update'])
            ->whereNumber('id')->whereNumber('destinationId');
        Route::delete('{id}/destinations/{destinationId}', [WebhookDestinationController::class, 'destroy'])
            ->whereNumber('id')->whereNumber('destinationId');

        // Logs: list/show/prune theo webhook.
        Route::get('{id}/requests', [WebhookRequestController::class, 'index'])->whereNumber('id');
        Route::get('{id}/stats', [WebhookRequestController::class, 'stats'])->whereNumber('id');
        Route::get('{id}/requests/{requestId}', [WebhookRequestController::class, 'show'])->whereNumber('id')->whereNumber('requestId');
        Route::post('{id}/requests/prune', [WebhookRequestController::class, 'prune'])->whereNumber('id');

        // Dispatch logs: what we sent out and results.
        Route::get('{id}/dispatches', [WebhookDispatchLogController::class, 'index'])->whereNumber('id');
        Route::get('{id}/dispatches/{dispatchId}', [WebhookDispatchLogController::class, 'show'])
            ->whereNumber('id')->whereNumber('dispatchId');
    });
});

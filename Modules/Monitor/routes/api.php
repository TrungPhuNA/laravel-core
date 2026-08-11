<?php

use Illuminate\Support\Facades\Route;
use Modules\Monitor\Http\Controllers\Api\V1\DomainCheckController;
use Modules\Monitor\Http\Controllers\Api\V1\DomainController;
use Modules\Monitor\Http\Controllers\Api\V1\MonitorSettingController;

Route::prefix('v1/monitor')->middleware('auth:sanctum')->group(function () {
    // Domains
    Route::get('domains', [DomainController::class, 'index'])->name('monitor.domains.index');
    Route::post('domains', [DomainController::class, 'store'])->name('monitor.domains.store');
    Route::post('domains/bulk', [DomainController::class, 'storeBulk'])->name('monitor.domains.bulk');
    Route::get('domains/{id}', [DomainController::class, 'show'])->name('monitor.domains.show')->whereNumber('id');
    Route::put('domains/{id}', [DomainController::class, 'update'])->name('monitor.domains.update')->whereNumber('id');
    Route::delete('domains/{id}', [DomainController::class, 'destroy'])->name('monitor.domains.destroy')->whereNumber('id');

    // Check + logs
    Route::post('domains/{id}/check', [DomainCheckController::class, 'check'])->name('monitor.domains.check')->whereNumber('id');
    Route::get('domains/{id}/logs', [DomainCheckController::class, 'logs'])->name('monitor.domains.logs')->whereNumber('id');

    // Settings
    Route::get('settings', [MonitorSettingController::class, 'show'])->name('monitor.settings.show');
    Route::put('settings', [MonitorSettingController::class, 'update'])->name('monitor.settings.update');
});
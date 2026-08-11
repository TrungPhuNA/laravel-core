<?php

use Illuminate\Support\Facades\Route;

// Monitor SPA (React) — quản lý domain & thời gian hết hạn.
Route::prefix('monitor')->group(function () {
    Route::view('/{any?}', 'monitor::app')->where('any', '.*');
});
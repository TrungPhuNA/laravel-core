<?php

use Illuminate\Support\Facades\Route;

// API-first: auth FE dung React SPA, goi API /api/v1/auth/*.
Route::prefix('auth')->group(function () {
    Route::view('/{any?}', 'auth::app')->where('any', '.*');
});

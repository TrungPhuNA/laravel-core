<?php

use Illuminate\Support\Facades\Route;

// Project nay la API-first (Sanctum token), chua tich hop web login/session (route('login')).
// Webhook management FE dung React SPA va goi API bang Bearer token (Sanctum).
Route::prefix('webhook')->group(function () {
    Route::view('/{any?}', 'webhook::app')->where('any', '.*');
});

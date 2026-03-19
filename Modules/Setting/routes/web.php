<?php

use Illuminate\Support\Facades\Route;

// Lưu ý:
// Project này là API-first (Sanctum token). Chưa tích hợp web auth (route('login')).
// Vì vậy không dùng middleware `auth` cho web route để tránh lỗi "Route [login] not defined".
//
// React SPA cho module Setting (prefix cố định).
Route::prefix('admin/settings')->group(function () {
    Route::view('/{any?}', 'setting::admin')->where('any', '.*');
});

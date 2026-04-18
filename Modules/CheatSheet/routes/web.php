<?php

use Illuminate\Support\Facades\Route;

// Lưu ý:
// Project này là API-first (Sanctum token). Chưa tích hợp web auth (route('login')).
// Vì vậy không dùng middleware `auth` cho web route để tránh lỗi "Route [login] not defined".
//
// React SPA cho module CheatSheet (prefix cố định).
Route::prefix('admin/cheat-sheets')->group(function () {
    Route::view('/{any?}', 'cheatsheet::admin')->where('any', '.*');
});

// Public SPA: browse cheat sheets public.
Route::prefix('cheat-sheets')->group(function () {
    Route::view('/{any?}', 'cheatsheet::public')->where('any', '.*');
});

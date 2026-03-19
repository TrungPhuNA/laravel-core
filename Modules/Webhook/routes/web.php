<?php

use Illuminate\Support\Facades\Route;

// Project nay la API-first (Sanctum token), chua tich hop web login/session.
// Vi vay module Webhook hien tai khong mo web routes de tranh loi redirect route('login').
Route::middleware([])->group(function () {
    // TODO: neu can admin SPA: tao prefix /admin/webhooks va view react tuong tu module Setting.
});

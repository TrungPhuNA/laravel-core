<?php

use Illuminate\Support\Facades\Route;

// Module này ưu tiên API. Nếu dự án cần web admin thì bổ sung routes tại đây.
Route::get('/users-module', fn () => 'User module');


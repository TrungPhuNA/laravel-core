<?php

use Illuminate\Support\Facades\Route;
use Modules\Ecommerce\Http\Controllers\Api\V1\Admin\CategoryController;
use Modules\Ecommerce\Http\Controllers\Api\V1\Admin\CustomerController;
use Modules\Ecommerce\Http\Controllers\Api\V1\Admin\DashboardController;
use Modules\Ecommerce\Http\Controllers\Api\V1\Admin\OrderController;
use Modules\Ecommerce\Http\Controllers\Api\V1\Admin\ProductController;
use Modules\Ecommerce\Http\Controllers\Api\V1\Admin\ShopController;

Route::prefix('v1/ecm')->middleware(['auth:sanctum', 'user_type:ADMIN,SYSTEM'])->group(function () {
    // Shops (không cần chọn shop context trước)
    Route::get('admin/shops', [ShopController::class, 'index'])->middleware('perm:ecommerce.shops.read');

    Route::prefix('admin')->middleware(['ecm_shop'])->group(function () {
        // Dashboard
        Route::get('dashboard/overview', [DashboardController::class, 'overview'])->middleware('perm:ecommerce.dashboard.read');
        Route::get('dashboard/revenue', [DashboardController::class, 'revenue'])->middleware('perm:ecommerce.dashboard.read');

        // Categories
        Route::get('categories', [CategoryController::class, 'index'])->middleware('perm:ecommerce.categories.read');
        Route::get('categories/{id}', [CategoryController::class, 'show'])->whereNumber('id')->middleware('perm:ecommerce.categories.read');
        Route::post('categories', [CategoryController::class, 'store'])->middleware('perm:ecommerce.categories.write');
        Route::put('categories/{id}', [CategoryController::class, 'update'])->whereNumber('id')->middleware('perm:ecommerce.categories.write');
        Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->whereNumber('id')->middleware('perm:ecommerce.categories.delete');

        // Products
        Route::get('products', [ProductController::class, 'index'])->middleware('perm:ecommerce.products.read');
        Route::get('products/{id}', [ProductController::class, 'show'])->whereNumber('id')->middleware('perm:ecommerce.products.read');
        Route::post('products', [ProductController::class, 'store'])->middleware('perm:ecommerce.products.write');
        Route::put('products/{id}', [ProductController::class, 'update'])->whereNumber('id')->middleware('perm:ecommerce.products.write');
        Route::delete('products/{id}', [ProductController::class, 'destroy'])->whereNumber('id')->middleware('perm:ecommerce.products.delete');

        // Customers
        Route::get('customers', [CustomerController::class, 'index'])->middleware('perm:ecommerce.customers.read');
        Route::get('customers/{id}', [CustomerController::class, 'show'])->whereNumber('id')->middleware('perm:ecommerce.customers.read');
        Route::post('customers', [CustomerController::class, 'store'])->middleware('perm:ecommerce.customers.write');
        Route::put('customers/{id}', [CustomerController::class, 'update'])->whereNumber('id')->middleware('perm:ecommerce.customers.write');
        Route::delete('customers/{id}', [CustomerController::class, 'destroy'])->whereNumber('id')->middleware('perm:ecommerce.customers.delete');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->middleware('perm:ecommerce.orders.read');
        Route::get('orders/{id}', [OrderController::class, 'show'])->whereNumber('id')->middleware('perm:ecommerce.orders.read');
        Route::post('orders', [OrderController::class, 'store'])->middleware('perm:ecommerce.orders.write');
        Route::put('orders/{id}', [OrderController::class, 'update'])->whereNumber('id')->middleware('perm:ecommerce.orders.write');
        Route::delete('orders/{id}', [OrderController::class, 'destroy'])->whereNumber('id')->middleware('perm:ecommerce.orders.delete');
    });
});

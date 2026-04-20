<?php

use App\Http\Controllers\V1\Admin\AdminCategoryController;
use App\Http\Controllers\V1\Admin\AdminProductController;
use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/send-code', [AuthController::class, 'sendVerificationCode'])
            ->middleware('throttle:2,1');
        Route::post('/verify-code', [AuthController::class, 'verifyAndLogin'])
            ->middleware('throttle:10,1');
        Route::post('/refresh', [AuthController::class, 'refreshAccessToken'])
            ->middleware('throttle:5,1');
        Route::middleware('auth:api')->group(function () {
            Route::patch('/complete-profile', [AuthController::class, 'completeUserProfile']);
            Route::post('/logout', [AuthController::class, 'userLogout']);
        });
    });

    Route::prefix('/menu')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{category_slug}', [ProductController::class, 'category']);
        Route::get('/{category_slug}/{product_slug}', [ProductController::class, 'show']);
    });

    Route::prefix('/admin')->middleware(['auth:api', 'admin'])->group(function () {
        Route::apiResource('/categories', AdminCategoryController::class);
        Route::apiResource('/products', AdminProductController::class);
    });
});

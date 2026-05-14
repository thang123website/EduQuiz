<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\VerifyApiKeyMiddleware::class])->group(function () {
    // API Cấu hình Public
    Route::get('v1/settings/website', [\App\Http\Controllers\Api\SettingController::class, 'getWebsiteSettings']);

    // Sliders
    Route::get('v1/sliders/{key}', [\App\Http\Controllers\Api\SliderController::class, 'show']);

    // Blog Categories
    Route::get('v1/blog-categories', [\App\Http\Controllers\Api\BlogCategoryController::class, 'index']);
    Route::get('v1/blog-categories/{slug}', [\App\Http\Controllers\Api\BlogCategoryController::class, 'show']);

    // Blogs
    Route::get('v1/blogs', [\App\Http\Controllers\Api\BlogController::class, 'index']);
    Route::get('v1/blogs/popular', [\App\Http\Controllers\Api\BlogController::class, 'popular']);
    Route::get('v1/blogs/{slug}/related', [\App\Http\Controllers\Api\BlogController::class, 'related']);
    Route::get('v1/blogs/{slug}', [\App\Http\Controllers\Api\BlogController::class, 'show']);

    Route::prefix('v1/auth')->group(function () {
        Route::post('/login', [\App\Http\Controllers\Api\Auth\AuthController::class, 'login']);
        Route::post('/register', [\App\Http\Controllers\Api\Auth\AuthController::class, 'register']);
        Route::post('/logout', [\App\Http\Controllers\Api\Auth\AuthController::class, 'logout'])->middleware('auth:sanctum');

        // Account Verification & Recovery
        Route::post('/verify-account', [\App\Http\Controllers\Api\Auth\AuthController::class, 'verifyAccount']);
        Route::post('/forgot-password', [\App\Http\Controllers\Api\Auth\AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [\App\Http\Controllers\Api\Auth\AuthController::class, 'resetPassword']);
    });

    Route::middleware('auth:sanctum')->prefix('v1/profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProfileController::class, 'show']);
        Route::post('/update', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
    });

    Route::middleware('auth:sanctum')->prefix('v1/notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::get('/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
        Route::post('/mark-all-as-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
        Route::post('/{id}/mark-as-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']);
    });
});

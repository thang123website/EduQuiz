<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\VerifyApiKeyMiddleware::class])->group(function () {
    Route::prefix('v1/auth')->group(function () {
        Route::post('/login', [\App\Http\Controllers\Api\Auth\AuthController::class, 'login']);
        Route::post('/register', [\App\Http\Controllers\Api\Auth\AuthController::class, 'register']);
        Route::post('/logout', [\App\Http\Controllers\Api\Auth\AuthController::class, 'logout'])->middleware('auth:sanctum');

        // Account Verification & Recovery
        Route::post('/verify-account', [\App\Http\Controllers\Api\Auth\AuthController::class, 'verifyAccount']);
        Route::post('/forgot-password', [\App\Http\Controllers\Api\Auth\AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [\App\Http\Controllers\Api\Auth\AuthController::class, 'resetPassword']);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});

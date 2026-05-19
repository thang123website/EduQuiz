<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\VerifyApiKeyMiddleware::class])->group(function () {
    // API Cấu hình Public
    Route::get('v1/settings/website', [\App\Http\Controllers\Api\SettingController::class, 'getWebsiteSettings']);
    Route::get('v1/translations/{lang}', [\App\Http\Controllers\Api\TranslationController::class, 'getTranslations']);

    // Sliders
    Route::get('v1/sliders/{key}', [\App\Http\Controllers\Api\SliderController::class, 'show']);

    // Blog Categories
    Route::get('v1/blog-categories', [\App\Http\Controllers\Api\BlogCategoryController::class, 'index']);
    Route::get('v1/blog-categories/{slug}', [\App\Http\Controllers\Api\BlogCategoryController::class, 'show']);

    // Blogs
    Route::get('v1/blogs', [\App\Http\Controllers\Api\BlogController::class, 'index']);
    Route::get('v1/blogs/latest', [\App\Http\Controllers\Api\BlogController::class, 'latest']);
    Route::get('v1/blogs/popular', [\App\Http\Controllers\Api\BlogController::class, 'popular']);
    Route::get('v1/blogs/{slug}/related', [\App\Http\Controllers\Api\BlogController::class, 'related']);
    Route::get('v1/blogs/{slug}', [\App\Http\Controllers\Api\BlogController::class, 'show']);
    Route::get('v1/blogs/{slug}/comments', [\App\Http\Controllers\Api\CommentController::class, 'index']);

    // Quizzes (Discovery)
    Route::get('v1/quiz-categories', [\App\Http\Controllers\Api\QuizCategoryController::class, 'index']);
    Route::get('v1/quizzes', [\App\Http\Controllers\Api\QuizController::class, 'index']);
    Route::get('v1/quizzes/latest', [\App\Http\Controllers\Api\QuizController::class, 'latest']);
    Route::get('v1/quizzes/popular', [\App\Http\Controllers\Api\QuizController::class, 'popular']);
    Route::get('v1/quizzes/{id}', [\App\Http\Controllers\Api\QuizController::class, 'show']);

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
        Route::post('/change-password', [\App\Http\Controllers\Api\ProfileController::class, 'changePassword']);
    });

    // Leaderboard (Public but could be auth, here public)
    Route::get('v1/leaderboard', [\App\Http\Controllers\Api\LeaderboardController::class, 'index']);

    // Auth required API
    Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
        // Comments
        Route::post('/blogs/{slug}/comments', [\App\Http\Controllers\Api\CommentController::class, 'store']);
        Route::put('/comments/{id}', [\App\Http\Controllers\Api\CommentController::class, 'update']);
        Route::delete('/comments/{id}', [\App\Http\Controllers\Api\CommentController::class, 'destroy']);

        // Bookmarks
        Route::get('/users/me/bookmarks', [\App\Http\Controllers\Api\BookmarkController::class, 'index']);
        Route::post('/quizzes/{id}/bookmark', [\App\Http\Controllers\Api\BookmarkController::class, 'toggleQuiz']);
        Route::post('/blogs/{slug}/bookmark', [\App\Http\Controllers\Api\BookmarkController::class, 'toggleBlog']);

        // Quizzes (Execution)
        Route::post('/quizzes/{id}/start', [\App\Http\Controllers\Api\QuizAttemptController::class, 'start']);
        Route::put('/attempts/{attempt_id}/autosave', [\App\Http\Controllers\Api\QuizAttemptController::class, 'autosave']);
        Route::post('/attempts/{attempt_id}/submit', [\App\Http\Controllers\Api\QuizAttemptController::class, 'submit']);
        Route::get('/attempts/{attempt_id}/result', [\App\Http\Controllers\Api\QuizAttemptController::class, 'result']);
        
        // Chatbot AI
        Route::post('/chatbot/message', [\App\Modules\Chatbot\Controllers\ChatbotController::class, 'sendMessage'])->middleware('throttle:15,1');

        // Quiz History & Dashboard
        Route::get('/quizzes/{id}/attempts', [\App\Http\Controllers\Api\UserDashboardController::class, 'quizAttempts']);
        Route::get('/users/me/attempts', [\App\Http\Controllers\Api\UserDashboardController::class, 'allAttempts']);
        Route::get('/users/me/statistics', [\App\Http\Controllers\Api\UserDashboardController::class, 'statistics']);
        Route::post('/users/me/targets', [\App\Http\Controllers\Api\UserDashboardController::class, 'updateTargets']);
    });

    Route::middleware('auth:sanctum')->prefix('v1/notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::get('/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
        Route::post('/mark-all-as-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
        Route::post('/{id}/mark-as-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']);
    });

    // Form Submissions
    Route::post('v1/forms/{type}', [\App\Http\Controllers\Api\FormSubmissionController::class, 'store'])->middleware('throttle:30,1');
});

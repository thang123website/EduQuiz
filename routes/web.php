<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return view('home-landing');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/set-locale/{locale}', function ($locale) {
    session()->put('locale', $locale);
    return redirect()->back();
})->name('set-locale');
// Avatar mặc định tự động tạo
Route::get('/getDefaultAvatar', [\App\Http\Controllers\DefaultAvatarController::class, 'make'])->name('default-avatar');
Route::middleware(['auth'])->group(function () {
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'deletePersonal'])->name('notifications.deletePersonal');
    Route::post('/notifications/bulk-delete', [\App\Http\Controllers\Admin\NotificationController::class, 'bulkDeletePersonal'])->name('notifications.bulkDeletePersonal');
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'userList'])->name('notifications.userList');
    Route::get('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::resource('blog-categories', \App\Http\Controllers\Admin\BlogCategoryController::class);
    Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class);

    // Cấu hình hệ thống
    Route::get('/settings/general', [\App\Http\Controllers\Admin\SettingController::class, 'general'])->name('settings.general');
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    // Cấu hình Email/SMTP
    Route::get('/settings/mail', [\App\Http\Controllers\Admin\SettingController::class, 'mailSettings'])->name('settings.mail');
    Route::put('/settings/mail', [\App\Http\Controllers\Admin\SettingController::class, 'mailSettingsUpdate'])->name('settings.mail.update');
    Route::post('/settings/mail/test', [\App\Http\Controllers\Admin\SettingController::class, 'testMailConnection'])->name('settings.mail.test');
    
    Route::get('/settings/api', [\App\Http\Controllers\Admin\SettingController::class, 'apiSettings'])->name('settings.api');

    // Language Manager
    Route::resource('languages', \App\Http\Controllers\Admin\LanguageController::class)->except(['show']);
    Route::get('languages/{code}/translations', [\App\Http\Controllers\Admin\LanguageController::class, 'translations'])->name('languages.translations');
    Route::post('languages/{code}/translations', [\App\Http\Controllers\Admin\LanguageController::class, 'updateTranslation'])->name('languages.translations.update');
    Route::post('languages/{code}/translate-single', [\App\Http\Controllers\Admin\LanguageController::class, 'translateSingle'])->name('languages.translate-single');
    Route::post('languages/auto-translate', [\App\Http\Controllers\Admin\LanguageController::class, 'autoTranslate'])->name('languages.auto-translate');
    Route::post('languages/auto-translate-data', [\App\Http\Controllers\Admin\LanguageController::class, 'autoTranslateData'])->name('languages.auto-translate-data');

    // Media Manager
    Route::prefix('media')->name('media.')->group(function () {
        Route::get('/',           [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('index');
        Route::post('/upload',    [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('upload');
        Route::get('/files',      [\App\Http\Controllers\Admin\MediaController::class, 'files'])->name('files');
        Route::get('/folders',    [\App\Http\Controllers\Admin\MediaController::class, 'folders'])->name('folders');
        Route::delete('/{file}',  [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [\App\Http\Controllers\Admin\MediaController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::post('/folders',   [\App\Http\Controllers\Admin\MediaController::class, 'createFolder'])->name('folders.store');
    });

    // Slider Manager
    Route::resource('sliders', \App\Http\Controllers\Admin\SliderController::class);
    Route::prefix('sliders/{slider}/items')->name('sliders.items.')->group(function () {
        Route::post('/',          [\App\Http\Controllers\Admin\SliderController::class, 'storeItem'])->name('store');
        Route::put('/{item}',     [\App\Http\Controllers\Admin\SliderController::class, 'updateItem'])->name('update');
        Route::delete('/{item}',  [\App\Http\Controllers\Admin\SliderController::class, 'destroyItem'])->name('destroy');
        Route::post('/reorder',   [\App\Http\Controllers\Admin\SliderController::class, 'reorderItems'])->name('reorder');
    });

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');
    Route::delete('/notifications/{history}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Comments Management
    Route::get('/comments', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('comments.index');
    Route::post('/comments', [\App\Http\Controllers\Admin\CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{id}/toggle', [\App\Http\Controllers\Admin\CommentController::class, 'toggleStatus'])->name('comments.toggle');
    Route::post('/comments/{id}/reply', [\App\Http\Controllers\Admin\CommentController::class, 'reply'])->name('comments.reply');
    Route::delete('/comments/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('comments.destroy');

    // Quiz Management
    Route::resource('quiz-categories', \App\Http\Controllers\Admin\QuizCategoryController::class);
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class);
    Route::resource('quizzes', \App\Http\Controllers\Admin\QuizController::class);
    Route::post('quizzes/{quiz}/parts', [\App\Http\Controllers\Admin\QuizPartController::class, 'store'])->name('quizzes.parts.store');
    Route::post('quizzes/parts/{part}/import-questions', [\App\Http\Controllers\Admin\QuestionImportController::class, 'import'])->name('quizzes.parts.import-questions');
    Route::post('quiz-parts/reorder', [\App\Http\Controllers\Admin\QuizPartController::class, 'reorder'])->name('quiz-parts.reorder');
    Route::put('quiz-parts/{part}', [\App\Http\Controllers\Admin\QuizPartController::class, 'update'])->name('quiz-parts.update');
    Route::delete('quiz-parts/{part}', [\App\Http\Controllers\Admin\QuizPartController::class, 'destroy'])->name('quiz-parts.destroy');
    Route::post('quiz-attempts/bulk-delete', [\App\Http\Controllers\Admin\QuizAttemptController::class, 'bulkDestroy'])->name('quiz-attempts.bulk-destroy');
    Route::resource('quiz-attempts', \App\Http\Controllers\Admin\QuizAttemptController::class)->only(['index', 'show', 'destroy']);
    Route::resource('questions', \App\Http\Controllers\Admin\QuestionController::class)->only(['show', 'store', 'update', 'destroy']);

    // Forms Management
    Route::resource('forms', \App\Http\Controllers\Admin\FormSubmissionController::class)->except(['create', 'store', 'edit']);
    Route::put('forms/{form}/status', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'updateStatus'])->name('forms.update-status');
});

// Public Comments
Route::middleware(['auth'])->group(function () {
    Route::post('/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
});

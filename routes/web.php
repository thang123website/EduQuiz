<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::resource('blog-categories', \App\Http\Controllers\Admin\BlogCategoryController::class);
    Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class);

    // Cấu hình hệ thống
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    // Cấu hình Email/SMTP
    Route::get('/settings/mail', [\App\Http\Controllers\Admin\SettingController::class, 'mailSettings'])->name('settings.mail');
    Route::put('/settings/mail', [\App\Http\Controllers\Admin\SettingController::class, 'mailSettingsUpdate'])->name('settings.mail.update');
    Route::post('/settings/mail/test', [\App\Http\Controllers\Admin\SettingController::class, 'testMailConnection'])->name('settings.mail.test');

    // Media Manager
    Route::prefix('media')->name('media.')->group(function () {
        Route::get('/',           [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('index');
        Route::post('/upload',    [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('upload');
        Route::get('/files',      [\App\Http\Controllers\Admin\MediaController::class, 'files'])->name('files');
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
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AddPostController;

Route::redirect('/', '/auth', 301);

Route::middleware('guest')->group(function() {
    Route::get('reg', [RegistrationController::class, 'index'])->name('reg.index');
    Route::post('reg', [RegistrationController::class, 'create']);
    Route::get('auth', [AuthController::class, 'index'])->name('auth.index');
    Route::post('auth', [AuthController::class, 'authenticate']);
});

Route::middleware('auth')->group(function() {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::get('addpost', [AddPostController::class, 'showAdd'])->name('post.add');
    Route::post('addpost', [AddPostController::class, 'doAdd'])->name('post.add_failed');

    Route::prefix('posts')->name('post')->group(function() {
        Route::get('/', [PostController::class, 'showList'])->name('.index');
        Route::get('page/{id}', [PostController::class, 'showList'])->name('.page')->whereNumber('id');
        Route::get('single/{id}', [PostController::class, 'showPost'])->name('.single')->whereNumber('id');
        Route::post('single/{id}', [PostController::class, 'addComment'])->name('.addcomment_failed')->whereNumber('id');
    });
});

Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::fallback(function () {
    echo "KEKW";
});


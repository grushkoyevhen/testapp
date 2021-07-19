<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;

Route::redirect('/', '/auth', 301);

Route::middleware('guest')->group(function() {
    Route::get('reg', [RegController::class, 'index'])->name('reg.index');
    Route::post('reg', [RegController::class, 'create']);
    Route::get('auth', [AuthController::class, 'index'])->name('auth.index');
    Route::post('auth', [AuthController::class, 'authenticate']);
});

Route::middleware('auth')->group(function() {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('addpost', [PostController::class, 'showAdd'])->name('post.add');
    Route::post('addpost', [PostController::class, 'add'])->name('post.add_failed');
    Route::get('post/{id}', [PostController::class, 'showPost'])->name('post.show')->whereNumber('id');

    Route::prefix('posts')->name('post.list')->group(function() {
        Route::get('/', [PostController::class, 'showList']);
        Route::get('page/{id}', [PostController::class, 'showList'])->name('.page')->whereNumber('id');
    });
});

Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::fallback(function () {
    echo "KEKW";
});


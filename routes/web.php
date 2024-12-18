<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

// Localized routes  
Route::prefix('{locale}')
    ->whereIn('locale', config('app.available_lang'))
    ->group(function () {
        Route::get('/', [Controllers\PageController::class, 'home'])->name('localized.home');

        Route::get('/posts', [Controllers\PostController::class, 'index'])->name('localized.post.archive');
        Route::get('/posts/{slug}', [Controllers\PostController::class, 'showLocalized'])->name('localized.post.single');

        Route::get('/categories', [Controllers\PostController::class, 'categories'])->name('localized.category.archive');
        Route::get('/categories/{slug}', [Controllers\PostController::class, 'category'])->name('localized.category.single');

        Route::get('/tags/{slug}', [Controllers\PostController::class, 'showLocalized'])->name('localized.tag.archive');

        Route::get('/{slug}', [Controllers\PageController::class, 'showLocalized'])->name('localized.page');

    });

// Non-localized routes
Route::get('/', [Controllers\PageController::class, 'home'])->name('home');
Route::get('/{slug}', [Controllers\PageController::class, 'show'])->name('page');
Route::get('/preview/{type}/{slug}', [Controllers\PreviewController::class, 'index']);


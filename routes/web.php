<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

// Localized routes  
Route::prefix('{locale}')
    ->whereIn('locale', config('app.available_lang'))
    ->group(function () {
        Route::get('/', [Controllers\PageController::class, 'home'])->name('localized.home');
        Route::get('/posts', [Controllers\PostController::class, 'index'])->name('localized.post.archive');
        Route::get('/{slug}', [Controllers\PageController::class, 'showLocalized'])->name('localized.page');


        // Route::get('/posts/{slug}', [Controllers\PostController::class, 'showLocalized'])->name('localized.post.single');
    
        Route::get('/categories/', [Controllers\PostController::class, 'showLocalized'])->name('localized.category.single');
        Route::get('/categories/{slug}', [Controllers\PostController::class, 'showLocalized'])->name('localized.category.archive');

        Route::get('/tags/', [Controllers\PostController::class, 'showLocalized'])->name('localized.tag.single');
        Route::get('/tags/{slug}', [Controllers\PostController::class, 'showLocalized'])->name('localized.tag.archive');
    });

// Non-localized routes
Route::get('/', [Controllers\PageController::class, 'home'])->name('home');
Route::get('/{slug}', [Controllers\PageController::class, 'show'])->name('page');
Route::get('/preview/{type}/{slug}', [Controllers\PreviewController::class, 'index']);


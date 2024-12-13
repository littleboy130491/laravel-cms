<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

// Localized routes  
Route::prefix('{locale}')
    ->whereIn('locale', config('app.lang_available'))
    ->group(function () {
        Route::get('/', [Controllers\PageController::class, 'home'])->name('localized.home');
        Route::get('/{slug}', [Controllers\PageController::class, 'show'])->name('localized.page');
        Route::get('/posts/{slug}', [Controllers\PostController::class, 'show'])->name('localized.post.single');
    });

// Non-localized routes
Route::get('/', [Controllers\PageController::class, 'home'])->name('home');
Route::get('/{slug}', [Controllers\PageController::class, 'show'])->name('page');

Route::get('/preview/{type}/{slug}', [Controllers\PreviewController::class, 'index']);


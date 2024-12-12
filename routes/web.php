<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

Route::get('/', [PageController::class, 'home']);

Route::get('/{locale}', [Controllers\PageController::class, 'home'])
    ->where('locale', 'en|id')
    ->name('home');

Route::get('/{slug: slug}', [Controllers\PageController::class, 'show']);
Route::get('/{locale}/{slug}', [Controllers\PageController::class, 'show']);

Route::get('/posts/{slug}', [Controllers\PostController::class, 'show']);

Route::get('/preview/{type}/{slug}', [Controllers\PreviewController::class, 'index']);


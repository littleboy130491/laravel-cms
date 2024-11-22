<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Models;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{slug}', [Controllers\PageController::class, 'show']);

Route::get('/posts/{slug}', [Controllers\PostController::class, 'show']);

Route::get('/preview/{type}/{slug}', function (string $type, string $slug) {

    switch ($type) {
        case 'templates':
            $preview_model = Models\Template::class;
            break;
        case 'partials':
            $preview_model = Models\Partial::class;
            break;
        default:
            $preview_model = Models\Template::class;
    }

    $name = $preview_model::where('slug', $slug)->firstOrFail()->slug;
    $componentName = $type . '.' . $name;

    return view('frontend.preview', ['componentName' => $componentName]);

});


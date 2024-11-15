<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Models\Template;
use Illuminate\Support\Facades\Blade;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{slug}', [Controllers\PageController::class, 'show']);

Route::get('/preview/{slug}', function (string $slug) {

    $template = Template::where('slug', $slug)
        ->firstOrFail();

    return view('frontend.preview', ['template' => $template->slug]);

});

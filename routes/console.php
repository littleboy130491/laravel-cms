<?php

use App\Models\Post;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    Post::where('status', Post::STATUS_SCHEDULED)
        ->where('published_at', '<=', now())
        ->update(['status' => Post::STATUS_PUBLISHED]);
})->everyMinute();

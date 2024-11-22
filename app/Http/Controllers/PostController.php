<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Blade;
class PostController extends Controller
{
    public function show(string $slug): View
    {
        $page = Post::where("slug", $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // remove .blade.php extension
        $templateName = $page->template ? str_replace(".blade.php", "", $page->template) : '';

        // blade content
        $bladeContent = Blade::render($page->content, ["page" => $page]);

        // add class to body html
        $modelName = Str::snake(class_basename(Post::class));
        $dataClass = "{$modelName} {$modelName}-id-{$page->id}";

        return view('frontend.page', [
            'page' => $page,
            'componentName' => $templateName,
            'content' => $bladeContent,
            'dataClass' => $dataClass,
            'modelName' => $modelName,
        ]);
    }
}

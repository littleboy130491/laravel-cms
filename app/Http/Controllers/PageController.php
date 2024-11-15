<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::where("slug", $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // remove .blade.php extension, uUse default template if not specified
        $template = $page->template ? str_replace(".blade.php", "", $page->template) : 'default';

        // blade content
        $bladeContent = Blade::render($page->content, ["page" => $page]);

        // add class to body html
        $modelName = Str::snake(class_basename(Page::class));
        $dataClass = "{$modelName} {$modelName}-id-{$page->id}";

        return view('frontend.page', [
            'page' => $page,
            'template' => $template,
            'content' => $bladeContent,
            'dataClass' => $dataClass,
        ]);
    }
}

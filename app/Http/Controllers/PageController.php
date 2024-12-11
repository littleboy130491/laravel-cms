<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Traits\DisplayViewWithCheckTemplates;
class PageController extends Controller
{

    use DisplayViewWithCheckTemplates;
    protected function getModelName(): string
    {
        return Str::snake(class_basename(Page::class));
    }

    public function show(string $slug): View
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return $this->displayViewSingle($page);
    }

    public function home(): View|string
    {
        $page = Page::where("slug", "home")
            ->where('status', 'published')
            ->first() ??
            Page::whereNotNull("slug")
                ->where('status', 'published')
                ->first();

        if (!$page) {
            return "Content empty, please create page first.";
        }

        return $this->displayViewSingle($page);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Traits\DisplayViewWithCheckTemplates;
use Illuminate\Support\Facades\App;

class PageController extends Controller
{

    use DisplayViewWithCheckTemplates;
    protected function getModelName(): string
    {
        return Str::snake(class_basename(Page::class));
    }

    public function show(?string $locale, string $slug): View
    {
        App::setLocale($locale);
        //dd($locale);
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return $this->displayViewSingle($page);
    }

    public function home(?string $locale = ''): View|string
    {
        App::setLocale($locale);

        $page = Page::where("slug", "home")
            ->where('status', 'published')
            ->first() ??
            Page::whereNotNull("slug")
                ->where('status', 'published')
                ->first();

        if (!$page) {
            return "Content empty, please create page first.";
        }
        // dd($page);
        return $this->displayViewSingle($page);
    }

}

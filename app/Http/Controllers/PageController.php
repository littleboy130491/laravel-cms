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

    public function show(?string $locale = config('app.locale'), string $slug): View
    {
        if ($locale) {
            App::setLocale($locale);
        }

        $page = Page::whereJsonContains('slug->' . $locale, $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return $this->displayViewSingle($page);
    }

    public function home(string $locale = ''): View|string
    {
        if ($locale) {
            App::setLocale($locale);
        }

        $page = Page::whereJsonContains('slug->' . config('app.locale'), config('app.homepage_slug_default_locale'))
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

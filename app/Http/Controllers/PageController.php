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

        $page = Page::whereJsonContains('slug->' . config('app.locale'), $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return $this->displayViewSingle($page);
    }

    public function showLocalized(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $page = Page::whereJsonContains('slug->' . $locale, $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return $this->displayViewSingle($page);
    }

    public function home(?string $locale = null): View|string
    {
        if ($locale) {
            app()->setLocale($locale);
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

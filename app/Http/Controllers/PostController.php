<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Traits\DisplayViewWithCheckTemplates;
use App\Traits\HasSearchQuery;
use Illuminate\Http\Request;
class PostController extends Controller
{
    use DisplayViewWithCheckTemplates, HasSearchQuery;
    protected function getModelName(): string
    {
        return Str::snake(class_basename(Post::class));
    }
    public function show(string $slug): View
    {
        $page = Post::where("slug", $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return $this->displayViewSingle($page);

    }

    public function showLocalized(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $page = Post::whereJsonContains('slug->' . $locale, $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return $this->displayViewSingle($page);
    }

    public function index(Request $request, string $locale)
    {
        app()->setLocale($locale);

        $query = Post::query()
            ->with(['author', 'categories', 'tags', 'featuredImage'])
            ->where('status', Post::STATUS_PUBLISHED);

        if ($request->has('search')) {
            $query = $this->searchQuery($request, $query);
        }

        $posts = $query->orderBy('published_at', 'desc')
            ->paginate(12)
            ->withQueryString(); // Preserves other query parameters in pagination links

        dd($posts);
        return view('posts.index', compact('posts'));
    }


}

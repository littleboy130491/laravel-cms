<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Traits\DisplayViewWithCheckTemplates;
use App\Traits\HasSearchQuery;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
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

    public function index(string $locale): View
    {
        app()->setLocale($locale);

        $query = $this->basicQuery();

        $posts = $this->orderPaginate($query);

        dd($posts);
        return view('posts.index', compact('posts'));
    }

    public function categories(string $locale): View
    {
        app()->setLocale($locale);

        $categories = Category::orderBy('title->' . $locale, 'asc')
            ->paginate(12);

        dd($categories);
        return view('posts.index', compact('posts'));
    }

    public function category(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $query = Post::whereHas('categories', function (Builder $query) use ($locale, $slug) {
            $query->where('slug->' . $locale, 'like', $slug);
        })->get();

        $posts = $query
            ->sortBy('title->' . $locale, 'asc')
            ->paginate(12);

        dd($posts);
        return view('posts.index', compact('posts'));
    }

    public function search(Request $request): View
    {

        $query = Post::query()
            ->with(['author', 'categories', 'tags', 'featuredImage'])
            ->where('status', Post::STATUS_PUBLISHED);

        if ($request->has('search')) {
            $query = $this->searchQuery($request, $query);
        }

        $record = $query->orderBy('published_at', 'desc')
            ->paginate(12)
            ->withQueryString(); // Preserves other query parameters in pagination links

        dd($record);
        return view('posts.index', compact('posts'));
    }

    private function basicQuery(): Builder
    {
        $query = Post::query()
            ->with(['author', 'categories', 'tags', 'featuredImage'])
            ->where('status', Post::STATUS_PUBLISHED);

        return $query;
    }

    private function orderPaginate(Builder|Collection $query, string $column = 'published_at', string $direction = 'desc', int $perPage = 12): LengthAwarePaginator
    {
        $query = $query->orderBy($column, $direction)
            ->paginate($perPage);

        return $query;
    }


}

<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Traits\DisplayViewWithCheckTemplates;
class PostController extends Controller
{
    use DisplayViewWithCheckTemplates;
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

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
use Illuminate\View\View;

class PreviewController extends Controller
{
    public function index(string $type, string $slug): View
    {

        switch ($type) {
            case 'templates':
                $preview_model = Models\Template::class;
                break;
            case 'partials':
                $preview_model = Models\Partial::class;
                break;
            default:
                $preview_model = Models\Template::class;
        }

        $name = $preview_model::where('slug', $slug)->firstOrFail()->slug;
        $componentName = $type . '.' . $name;

        return view('frontend.preview', ['componentName' => $componentName]);
    }
}

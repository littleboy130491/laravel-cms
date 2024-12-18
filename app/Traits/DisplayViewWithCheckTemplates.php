<?php

namespace App\Traits;

use App\Models\Page;
use App\Models\Post;
use App\Models\Template;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;

trait DisplayViewWithCheckTemplates
{
    protected function displayViewSingle(Post|Page $page): View
    {
        $templateName = $this->checkTemplate($page->template ?? '');

        // add class to body html
        $modelName = $this->getModelName() ?? '';
        $dataClass = $modelName ? "{$modelName} {$modelName}-id-{$page->id}" : '';


        return view('frontend.page', [
            'page' => $page,
            'componentName' => $templateName,
            'content' => $page->content,
            'dataClass' => $dataClass,
            'modelName' => $modelName,
        ]);
    }
    protected function checkTemplate(string $name): string
    {

        // remove .blade.php extension
        $slug = str_replace(".blade.php", "", $name);

        // find assigned template from CMS
        if ($slug && Template::where('slug', $slug)?->first()?->is_active) {
            $templateName = $slug;
        } else
            // fallback template from CMS for model name, ex: post or page
            if (Template::where('slug', $this->getModelName())?->first()?->is_active) {
                $templateName = $this->getModelName();
            } else
                // fallback template from CMS for default
                if (Template::where('slug', 'default')?->first()?->is_active) {
                    $templateName = 'default';
                } else
                    $templateName = '';

        return $templateName;

    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Str;
class Page extends Model
{
    use SoftDeletes, HasSEO;

    protected $fillable = [
        'title',
        'slug',
        'meta',
        'content',
        'status',
        'head_code',
        'body_code',
        'template',
    ];
    public static $slugPath = '';
    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];

    }
    public function getDynamicSEOData(): SEOData
    {
        $title = $this->seo->title
            ?? Str::limit(
                $this->title ?? null,
                60,
                '',
                preserveWords: true
            );
        $description = $this->seo->description
            ?? Str::limit(
                strip_tags($this->content ?? null),
                160,
                '',
                preserveWords: true
            );
        // Override only the properties you want:
        return new SEOData(
            title: $this->title,
            description: strip_tags($this->content),
        );
    }
}

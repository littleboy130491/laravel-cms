<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
class Page extends Model
{
    use SoftDeletes, HasSEO, HasTranslations;

    protected $fillable = [
        'title',
        'slug',
        'meta',
        'content',
        'excerpt',
        'status',
        'head_code',
        'body_code',
        'template',
        'featured_image',
    ];
    public static $slugPath = '';
    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];

    }
    public $translatable = [
        'title',
        'slug',
        'meta',
        'excerpt',
    ];
    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'featured_image');
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
            ?? $this->excerpt;

        $image = $this->seo?->image
            ?? $this->featuredImage?->path
            ?? null;
        // Override only the properties you want:
        return new SEOData(
            title: $title,
            description: $description,
            image: $image,
        );
    }
}

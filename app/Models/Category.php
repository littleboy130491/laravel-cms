<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Str;
class Category extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'featured_image',
        'parent_id',
        'order_column',
    ];

    public $translatable = [
        'title',
        'slug',
        'description',
    ];

    protected $casts = [
        'order_column' => 'integer',
    ];
    public static $slugPath = 'categories';
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }

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
            ?? $this->excerpt
            ?? Str::limit(
                strip_tags($this->content ?? null),
                160,
                '',
                preserveWords: true
            );
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
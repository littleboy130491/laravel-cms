<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Str;
class Tag extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    protected $fillable = [
        'title',
        'slug',
        'description',
    ];

    public $translatable = [
        'title',
        'slug',
        'description',
    ];
    public static $slugPath = 'tags';
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
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

        return new SEOData(
            title: $title,
            description: $description,
        );
    }
}
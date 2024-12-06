<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
class Post extends Model
{
    use HasFactory, SoftDeletes, HasSEO, HasTranslations;


    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'author_id',
        'status',
        'published_at',
        'is_featured',
        'order_column',
        'head_code',
        'body_code',
        'template',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'order_column' => 'integer',
        'published_at' => 'datetime',
    ];

    public $translatable = [
        'title',
        'slug',
        'content',
        'excerpt',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PUBLISHED = 'published';
    public static $slugPath = 'posts';
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_PUBLISHED => 'Published',
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SCHEDULED => 'warning',
            self::STATUS_PUBLISHED => 'success',
        ];
    }

    protected $attributes = [
        'status' => self::STATUS_DRAFT,
        'is_featured' => false,
    ];

    // Relationships
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
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
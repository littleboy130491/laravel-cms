<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

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
}
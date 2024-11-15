<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

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
}

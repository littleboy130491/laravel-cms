<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partial extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'content',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

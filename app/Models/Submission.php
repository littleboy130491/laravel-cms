<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'fields'
    ];

    protected $casts = [
        'fields' => 'array',
    ];
}

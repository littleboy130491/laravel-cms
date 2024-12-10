<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $levels = [
        'super_admin' => 99,
        'admin' => 90,
        'editor' => 80,
    ];

    public function getLevel(): int
    {
        return $this->levels[$this->name] ?? 0;
    }

}
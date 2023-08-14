<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class CustomRole extends SpatieRole
{
    use HasFactory;

    public function scopeAllRoles($query)
    {
        return $query->where('id', '!=', 5)->get(['id', 'nickname']);
    }
}

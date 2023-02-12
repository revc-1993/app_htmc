<?php

namespace App\Models;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'position_name',
        'department_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id'); //->withPivot();
    }

    public function users()
    {
        return $this->hasMany(User::class, 'job_position_id'); //->withPivot();
    }
}

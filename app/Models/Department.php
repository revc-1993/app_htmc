<?php

namespace App\Models;

use App\Models\JobPosition;
use App\Models\Certification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department',
    ];


    public function jobPositions()
    {
        return $this->hasMany(JobPosition::class, 'department_id'); //->withPivot();
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class, 'department_id'); //->withPivot();
    }
}

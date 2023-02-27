<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessType extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_type',
    ];

    public function certifications()
    {
        return $this->hasMany(Certification::class, 'process_type_id'); //->withPivot();
    }
}

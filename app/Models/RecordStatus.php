<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
    ];

    public function certifications()
    {
        return $this->hasMany(Certification::class, 'record_status'); //->withPivot();
    }
}

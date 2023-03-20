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
        return $this->hasMany(Certification::class, 'record_status_id'); //->withPivot();
    }

    public function scopeGetRecordStatus($query)
    {
        $role = $this->getRole();

        if ($role === 3) {
            $operator = $role === 3 ? "<=" : ">";
            $query->where('id', $operator, 4);
        } else if ($role === 4) {
            $query;
        }
    }

    protected function getRole()
    {
        return auth()->user()->roles()->first()->id;
    }
}

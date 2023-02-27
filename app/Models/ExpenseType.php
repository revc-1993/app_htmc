<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_type',
    ];

    public function certifications()
    {
        return $this->hasMany(Certification::class, 'expense_type_id'); //->withPivot();
    }
}

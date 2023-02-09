<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_object',
        'requesting_area',   // Posiblemente FK
        'amount',
        'reception_date',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_object',
        'requesting_area',   // Posiblemente FK
        'amount',
        'reception_date',
    ];

    public function scopePending($query)
    {
        $query->where('management_status', '<>', 'Observado');
    }
}

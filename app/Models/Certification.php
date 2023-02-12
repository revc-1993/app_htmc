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
        'customer_id',
    ];

    /**
     * Get the User that owns the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the Department that owns the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function scopePending($query)
    {
        $query->where('management_status', '<>', 'Observado');
    }
}

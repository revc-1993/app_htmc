<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commitment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "process_code",
        "vendor_name",
        "contract_administrator",
        "amount_to_commit",
        'management_status',  // Considerar posibilidad de FK a tabla ESTADOS
        "certification_id",
    ];

    /**
     * Get the User that owns the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function certification()
    {
        return $this->belongsTo(Certification::class, 'certification_id');
    }
}

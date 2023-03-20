<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commitment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "commitment_memo",
        "contract_administrator",
        "assignment_date",

        "commitment_date",
        'nit_name',
        'commitment_amount',
        "commitment_comments",

        "current_management",
        "record_status_id",
        "certification_id",
        "customer_id",
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

    /**
     * Get the User that owns the Commitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}

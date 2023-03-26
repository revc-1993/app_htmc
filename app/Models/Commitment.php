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
        "process_number",
        "contract_administrator",
        "assignment_date",
        "customer_id",
        "japc_comments",

        "vendor_id",
        "commitment_amount",
        "commitment_cur",
        "commitment_comments",
        "commitment_date",

        "treasury_approved",
        "returned_document_number",
        "coord_cgf_date",
        "coord_cgf_comments",

        "current_management",
        "record_status_id",
        "certification_id",
    ];

    /**
     * Get the User that owns the Commitment
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

    /**
     * Get the RecordStatus that owns the Commitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get the RecordStatus that owns the Commitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recordStatus()
    {
        return $this->belongsTo(RecordStatus::class, 'record_status_id');
    }

    public function scopeFiltered($query)
    {
        $query->orderBy("commitments.id", "desc");
    }
}

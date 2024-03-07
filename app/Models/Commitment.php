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
        "contract_administrator_id",
        "purchase_order",
        "sec_cgf_date",
        "contract_number",
        "sec_cgf_comments",

        "assignment_date",
        "customer_id",
        "japc_comments",

        "vendor_id",
        "commitment_amount",
        "balance",
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
     * Get the CurrentManagement that owns the Commitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentManagement()
    {
        return $this->belongsTo(CurrentManagement::class, 'current_management');
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
     * Get the Vendor that owns the Commitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get the ContractAdministrator that owns the Commitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contractAdministrator()
    {
        return $this->belongsTo(ContractAdministrator::class, 'contract_administrator_id');
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

    public function scopeApproved($query, $request)
    {
        $query->where('commitment_cur', $request)->where('record_status_id', '=', 6);
    }

    public function scopeFiltered($query)
    {
        $query->orderBy("commitments.id", "desc");
    }

    public function accruals()
    {
        return $this->hasMany(Accrual::class, 'commitment_id');
    }
}

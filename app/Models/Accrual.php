<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accrual extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "accrual_memo",
        "voucher_number",
        "sec_cgf_comments",
        "sec_cgf_date",

        "assignment_date",
        "japc_comments",
        "customer_id",

        "commitment_id",
        "accrual_cur",
        "accrual_date",
        "record_status_id",
        "accrual_amount",
        "accrual_comments",

        "treasury_approved",
        "returned_document_number",
        "coord_cgf_date",
        "coord_cgf_comments",
        "current_management",
    ];

    public function commitment()
    {
        return $this->belongsTo(Commitment::class, 'commitment_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'accrual_id');
    }

    /**
     * Get the CurrentManagement that owns the Accrual
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentManagement()
    {
        return $this->belongsTo(CurrentManagement::class, 'current_management');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function recordStatus()
    {
        return $this->belongsTo(RecordStatus::class, 'record_status_id');
    }
}

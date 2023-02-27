<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // SECRETARÍA COORDINACION FINANCIERA
        'certification_memo',
        'content',
        'contract_object',
        'process_type_id',
        'expense_type_id',
        'department_id',
        'cgf_comments',
        'cgf_date',

        // SECRETARÍA JAPC-CP
        'assignment_date',
        'japc_comments',

        // ANALISTA DE CERTIFICACIÓN
        'process_number',
        'nit_name',
        'cp_date',
        'budget_line',
        'certified_amount',
        'certification_status',
        'certification_comments',

        // TESORERÍA
        'treasury_approved',
        'returned_document_number',

        // CONTROL TOTAL
        'record_status',
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

    /**
     * Get the Department that owns the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recordStatus()
    {
        return $this->belongsTo(Department::class, 'record_status');
    }

    /**
     * Get the ProcessType that owns the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function processType()
    {
        return $this->belongsTo(ProcessType::class, 'process_type_id');
    }

    /**
     * Get the ExpenseType that owns the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }


    /**
     * Get all of the Certifications for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commitments()
    {
        return $this->hasMany(Commitment::class, 'certification_id');
    }


    public function scopePending($query)
    {
        $query->where('record_status', '<>', 'Observado')->orderBy("certifications.id", "desc");
    }

    public function scopeNotReviewed($query)
    {
        $query->where('record_status', '<>', 'Observado')->orderBy("certifications.cgf_date", "asc");
    }

    public function scopeAmountOrdered($query)
    {
        $query->where('record_status', '=', 'Certificado')->orderBy("certifications.certified_amount", "desc");
    }
}

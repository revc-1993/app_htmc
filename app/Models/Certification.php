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
        'sec_cgf_date',
        'sec_cgf_comments',

        // SECRETARÍA JAPC-CP
        'assignment_date',
        'japc_comments',

        // ANALISTA DE CERTIFICACIÓN
        'process_number',
        'vendor_id',
        'cp_date',
        'budget_line_id',
        'certified_amount',
        'certification_number',
        'certification_comments',

        //  COORDINACION GENERAL FINANCIERA
        'treasury_approved',
        'returned_document_number',
        'coord_cgf_date',
        'coord_cgf_comments',

        // CONTROL TOTAL
        'current_management',
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
     * Get the BudgetLine that owns the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budgetLine()
    {
        return $this->belongsTo(BudgetLine::class, 'budget_line_id');
    }

    /**
     * Get the RecordStatus that owns the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recordStatus()
    {
        return $this->belongsTo(RecordStatus::class, 'record_status');
    }

    /**
     * Get the RecordStatus that owns the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
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

    public function scopeFiltered($query)
    {
        $role = auth()->user()->roles()->first()->id;
        $user = auth()->user()->id;
        if ($role === 3)
            $query->where("certifications.customer_id", $user);
        else if ($role === 4)
            $query->where("certifications.current_management", $role);

        $query->orderBy("certifications.id", "desc");
    }

    public function scopeNotReviewed($query)
    {
        $query->where('record_status', '<>', 'Observado')->orderBy("certifications.sec_cgf_date", "asc");
    }

    public function scopeAmountOrdered($query)
    {
        $query->where('record_status', '=', 'Certificado')->orderBy("certifications.certified_amount", "desc");
    }
}

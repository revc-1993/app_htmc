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
        'process_type',
        'contract_object',
        'expense_type',
        'cgf_comments',
        'cgf_date',
        'department_id',

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

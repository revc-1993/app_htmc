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
        'reception_date',
        'amount',
        'department_id',
        'customer_id',
        'certification_number',
        'assignment_date',
        'japc_reassignment_date',
        'budget_line',
        'amount_to_commit',
        'obligation_type',
        'process_type',
        'comments',
        'management_status',
        'last_validation',
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
        $query->where('management_status', '<>', 'Observado')->orderBy("certifications.id", "desc");
    }

    public function scopeNotReviewed($query)
    {
        $query->where('management_status', '<>', 'Observado')->orderBy("certifications.reception_date", "asc");
    }

    public function scopeAmountOrdered($query)
    {
        $query->where('management_status', '=', 'Certificado')->orderBy("certifications.amount", "desc");
    }
}

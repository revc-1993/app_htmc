<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CertificationBudgetLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'certification_id',
        'budget_line_id',
    ];

    /**
     * Get the CertificationBudgetLine that owns the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function certification()
    {
        return $this->belongsTo(Certification::class, 'certification_id');
    }

    /**
     * Get the CertificationBudgetLine that owns the BudgetLine
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function budgetLine()
    {
        return $this->belongsTo(BudgetLine::class, 'budget_line_id');
    }
}

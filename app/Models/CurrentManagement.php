<?php

namespace App\Models;

use App\Constants\LaborManagements;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\Modules;

class CurrentManagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'module',
        'treasury_name',
        'step'
    ];

    /**
     * Get all of the CurrentManagements for the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function certifications()
    {
        return $this->hasMany(Certification::class, 'current_management');
    }

    /**
     * Get all of the CurrentManagements for the Commitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commitments()
    {
        return $this->hasMany(Commitment::class, 'current_management');
    }

    /**
     * Get all of the CurrentManagements for the Commitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accruals()
    {
        return $this->hasMany(Accrual::class, 'current_management');
    }

    public function scopeAllProfiles($query, $module = "")
    {
        $operator = $module === Modules::TREASURY ? "=" : "!=";
        $query->where('module', $operator, Modules::TREASURY);
    }
}

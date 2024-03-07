<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractAdministrator extends Model
{
    use HasFactory;

    protected $fillable = [
        'ci',
        'name',
        'last_name',
        'names',
    ];

    /**
     * Get all of the ContractAdministrators for the Commitment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commitments()
    {
        return $this->hasMany(Commitment::class, 'contract_administrator_id');
    }
}

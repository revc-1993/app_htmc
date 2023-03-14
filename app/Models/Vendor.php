<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nit',
        'name',
    ];

    /**
     * Get all of the Vendors for the Certification
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function certifications()
    {
        return $this->hasMany(Certification::class, 'vendor_id');
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'department',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $dates = [
        'admin_since' => 'datetime',
    ];

    /**
     * Get all of the Certifications for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function certifications()
    {
        return $this->hasMany(Certification::class, 'customer_id');
    }

    /**
     * Get all of the Certifications for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commitments()
    {
        return $this->hasMany(Commitment::class, 'customer_id');
    }

    public function scopeAnalystCertification($query)
    {
        $query->select('users.id', 'users.name')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', '=', 3);
    }
}

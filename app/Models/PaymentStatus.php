<?php

namespace App\Models;

use App\Constants\ManagementPaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
    ];

    public function managerStatuses()
    {
        return $this->hasMany(Payment::class, 'manager_status');
    }

    public function analystStatuses()
    {
        return $this->hasMany(Payment::class, 'analyst_status');
    }

    public function scopeGetRecordStatus($query)
    {
        $operator = $this->getRole() === "treasury_analyst_role"
            ? "<" : "<=";
        $query->where('payment_statuses.id', $operator, ManagementPaymentStatus::PAID);
    }

    protected function getRole()
    {
        return auth()->user()->roles->first()->name;
    }
}

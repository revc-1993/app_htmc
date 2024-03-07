<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        "accrual_id",
        "customer_id",
        "manager_status",
        "manager_comments",
        "manager_date",
        "treasury_approved",

        "analyst_status",
        "analyst_comments",
        "analyst_date",

        "current_management",
    ];

    public function accrual()
    {
        return $this->belongsTo(Accrual::class, 'accrual_id');
    }

    public function managerStatus()
    {
        return $this->belongsTo(PaymentStatus::class, 'manager_status');
    }

    public function analystStatus()
    {
        return $this->belongsTo(PaymentStatus::class, 'analyst_status');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function currentManagement()
    {
        return $this->belongsTo(CurrentManagement::class, 'current_management');
    }
}

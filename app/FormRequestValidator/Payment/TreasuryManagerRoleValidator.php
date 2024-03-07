<?php

namespace App\FormRequestValidator\Payment;

class TreasuryManagerRoleValidator implements PaymentRoleValidatorInterface
{
    public function getRules(): array
    {
        return [
            'customer_id' => ['required'],
            'manager_comments' => ['nullable'],
            'treasury_approved' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'customer_id' => "Usuario",
            'manager_comments' => "Observaciones",
            'treasury_approved' => "Estado de aprobación",
        ];
    }

    public function getMessages(): array
    {
        return [];
    }
}

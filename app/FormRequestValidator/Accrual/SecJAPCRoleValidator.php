<?php

namespace App\FormRequestValidator\Accrual;

class SecJAPCRoleValidator implements AccrualRoleValidatorInterface
{
    public function getRules(): array
    {
        return [
            'contract_administrator' => ['required'],
            'customer_id' => ['required'],
            'japc_comments' => ['nullable'],
        ];
    }
}

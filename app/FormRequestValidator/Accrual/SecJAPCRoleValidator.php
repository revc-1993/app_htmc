<?php

namespace App\FormRequestValidator\Accrual;

class SecJAPCRoleValidator implements AccrualRoleValidatorInterface
{
    public function getRules(): array
    {
        return [
            'accrual_memo' => [
                'nullable',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
            ],
            'customer_id' => ['required'],
            'japc_comments' => ['nullable'],
        ];
    }
}

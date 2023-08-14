<?php

namespace App\FormRequestValidator\Commitment;

class SecJAPCRoleValidator implements CommitmentRoleValidatorInterface
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

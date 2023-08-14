<?php

namespace App\FormRequestValidator\Commitment;

class SecCGFRoleValidator implements CommitmentRoleValidatorInterface
{
    public function getRules(): array
    {
        return [
            'commitment_memo' => [
                'nullable',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
            ],
            'process_number' => ['nullable', 'alphadash', 'string', 'min:15', 'max:100'],
            'contract_administrator' => ['required'],
            'contract_number' => ['required'],
            'sec_cgf_comments' => ['nullable'],
        ];
    }
}

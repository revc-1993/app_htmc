<?php

namespace App\FormRequestValidator\Accrual;

use App\Rules\ValidateAccrualAmount;

class AnalystRoleValidator implements AccrualRoleValidatorInterface
{
    private $inputData;

    public function __construct(array $inputData)
    {
        $this->inputData = $inputData;
    }

    public function getRules(): array
    {
        return [
            'commitment_id' => ['required'],
            'accrual_cur' => ['required_if:record_status_id,5', 'numeric', 'min:1', 'nullable'],
            'record_status_id' => ['required', 'in:1,2,3,4,5'],
            'accrual_amount' => ['nullable', 'min:0', new ValidateAccrualAmount($this->inputData['commitment_id'])],
            'accrual_comments' => ['nullable'],
        ];
    }
}

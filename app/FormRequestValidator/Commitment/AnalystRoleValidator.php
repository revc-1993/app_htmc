<?php

namespace App\FormRequestValidator\Commitment;

use App\Rules\ValidateCommitmentAmount;

class AnalystRoleValidator implements CommitmentRoleValidatorInterface
{
    private $inputData;

    public function __construct(array $inputData)
    {
        $this->inputData = $inputData;
    }

    public function getRules(): array
    {
        return [
            'certification_id' => ['required'],
            'vendor_id' => ['nullable'],
            'commitment_amount' => ['nullable', 'min:0', new ValidateCommitmentAmount($this->inputData['certification_id'])],
            'record_status_id' => ['required', 'in:1,2,3,4,5'],
            'commitment_cur' => ['required_if:record_status_id,5', 'numeric', 'min:1', 'nullable'],
            'commitment_comments' => ['nullable'],
        ];
    }
}

<?php

namespace App\FormRequestValidator\Certification;

class AnalystRoleValidator implements CertificationRoleValidatorInterface
{
    private $inputData;

    public function __construct(array $inputData)
    {
        $this->inputData = $inputData;
    }

    public function getRules(): array
    {
        return [
            'process_number' => ($this->inputData['expense_type_id'] === 1 ? ['nullable'] : ['required']) + ['alphadash', 'string', 'min:15', 'max:100'],
            'vendor_id' => ($this->inputData['expense_type_id'] === 1 ? ['nullable'] : ['required']),
            'budget_line_id' => ['required'],
            'certified_amount' => ['nullable', 'numeric', 'min:0'],
            'certification_number' => ['required_if:record_status_id,5', 'numeric', 'min:1', 'nullable'],
            'record_status_id' => ['required', 'in:1,2,3,4,5'],
            'certification_comments' => ['nullable'],
            'current_management' => ['nullable'],
        ];
    }
}

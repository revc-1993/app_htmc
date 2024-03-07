<?php

namespace App\FormRequestValidator\Certification;

use App\Rules\UniqueNumberCurInYear;
use App\Constants\Modules;

class AnalystRoleValidator implements CertificationRoleValidatorInterface
{
    private $currentRecordId;

    public function __construct($currentRecordId)
    {
        $this->currentRecordId = $currentRecordId;
    }

    public function getRules(): array
    {
        return [
            // 'vendor_id' => ($this->inputData['expense_type_id'] === 1 ? ['nullable'] : ['required']),
            'budget_line_id' => ['required'],
            'certified_amount' => ['nullable', 'numeric', 'min:0', 'required_if:record_status_id,5'],
            'certification_number' => [
                new UniqueNumberCurInYear(Modules::CERTIFICATIONS, $this->currentRecordId),
                'required_if:record_status_id,5',
                'numeric',
                'min:1',
                'nullable'
            ],
            'record_status_id' => ['required', 'in:1,2,3,4,5'],
            'certification_comments' => ['nullable'],
            'current_management' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'budget_line_id' => "Ítem Presupuestario",
            'certified_amount' => "Monto Certificado",
            'certification_number' => "Nro. de Certificación",
            'record_status_id' => "Estado de Certificación",
            'certification_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [
            'certified_amount.required_if' => "El campo :attribute es requerido",
            'certification_number.required_if' => "El campo :attribute es requerido",
        ];
    }
}

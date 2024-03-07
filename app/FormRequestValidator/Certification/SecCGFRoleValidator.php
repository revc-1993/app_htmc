<?php

namespace App\FormRequestValidator\Certification;

class SecCGFRoleValidator implements CertificationRoleValidatorInterface
{
    public function getRules(): array
    {
        return [
            'certification_memo' => [
                'nullable',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
            ],
            'content' => ['nullable', 'min:8'],
            'contract_object' => ['required', 'string', 'min:15', 'max:255'],
            'process_type_id' => ['required'],
            'expense_type_id' => ['required'],
            'department_id' => ['required'],
            'sec_cgf_comments' => ['nullable'],
            'current_management' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'certification_memo' => "Nro. de Memorando",
            'content' => "Contenido de Expediente",
            'contact_object' => "Objeto de Contrato",
            'procces_type_id' => "Tipo de Proceso",
            'expense_type_id' => "Tipo de Gasto",
            'department_id' => "Area Requirente",
            'sec_cgf_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [];
    }
}

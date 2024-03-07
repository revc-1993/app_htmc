<?php

namespace App\FormRequestValidator\Certification;

class CoordCGFRoleValidator implements CertificationRoleValidatorInterface
{
    public function getRules(): array
    {
        return [
            'treasury_approved' => ['required'],
            'returned_document_number' => [
                'required',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/',
            ],
            'coord_cgf_comments' => ['nullable'],
            'current_management' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'treasury_approved' => "Estado Final de Expediente",
            'returned_document_number' => "Nro. de Memorando",
            'coord_cgf_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [];
    }
}

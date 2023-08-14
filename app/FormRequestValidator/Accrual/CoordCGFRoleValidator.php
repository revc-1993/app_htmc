<?php

namespace App\FormRequestValidator\Accrual;

class CoordCGFRoleValidator implements AccrualRoleValidatorInterface
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
        ];
    }
}

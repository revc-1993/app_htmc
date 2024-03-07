<?php

namespace App\FormRequestValidator\Accrual;

class SecCGFRoleValidator implements AccrualRoleValidatorInterface
{
    public function getRules(): array
    {
        return [
            'accrual_memo' => [
                'nullable',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
            ],
            'voucher_number' => ['nullable', 'regex:/^[A-Za-z0-9 _-]+$/', 'string', 'min:15', 'max:100'],
            'sec_cgf_comments' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'accrual_memo' => "Nro. de Memorando",
            'voucher_number' => "Número de Comprobante",
            'sec_cgf_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [];
    }
}

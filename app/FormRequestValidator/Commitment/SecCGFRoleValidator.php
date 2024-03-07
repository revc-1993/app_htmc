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
            'contract_administrator_id' => ['required'],
            'contract_number' => ['required'],
            'purchase_order' => ['nullable'],
            'sec_cgf_comments' => ['nullable'],
            // 'current_management' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'commitment_memo' => "Nro. de Memorando",
            'process_number' => "Número de Proceso",
            'contract_administrator_id' => "Administrador de Contrato - Orden de Compra",
            'contract_number' => "Número de Contrato - Convenio Marco",
            'purchase_order' => "Orden de Compra",
            'sec_cgf_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [];
    }
}

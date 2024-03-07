<?php

namespace App\FormRequestValidator\Commitment;

class SecJAPCRoleValidator implements CommitmentRoleValidatorInterface
{
    public function getRules(): array
    {
        return [
            'process_number' => ['nullable', 'alphadash', 'string', 'min:15', 'max:100'],
            'customer_id' => ['required'],
            'japc_comments' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'proccess_number' => "Número de Proceso",
            'customer_id' => "Usuario",
            'japc_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [];
    }
}

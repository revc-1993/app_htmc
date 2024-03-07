<?php

namespace App\FormRequestValidator\Certification;

class SecJAPCRoleValidator implements CertificationRoleValidatorInterface
{
    public function getRules(): array
    {
        return [
            'content' => ['nullable', 'min:10'],
            'customer_id' => ['required'],
            'japc_comments' => ['nullable'],
            'current_management' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'content' => "Contenido de Expediente",
            'customer_id' => "Usuario",
            'japc_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [];
    }
}

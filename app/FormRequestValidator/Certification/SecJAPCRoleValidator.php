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
}

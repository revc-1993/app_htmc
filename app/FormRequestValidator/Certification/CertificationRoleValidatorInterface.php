<?php

namespace App\FormRequestValidator\Certification;

interface CertificationRoleValidatorInterface
{
    public function getRules(): array;
    public function getAttributes(): array;
    public function getMessages(): array;
}

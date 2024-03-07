<?php

namespace App\FormRequestValidator\Accrual;

interface AccrualRoleValidatorInterface
{
    public function getRules(): array;
    public function getAttributes(): array;
    public function getMessages(): array;
}

<?php

namespace App\FormRequestValidator\Payment;

interface PaymentRoleValidatorInterface
{
    public function getRules(): array;
    public function getAttributes(): array;
    public function getMessages(): array;
}

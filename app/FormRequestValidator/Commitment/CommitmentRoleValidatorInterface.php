<?php

namespace App\FormRequestValidator\Commitment;

interface CommitmentRoleValidatorInterface
{
    public function getRules(): array;
    public function getAttributes(): array;
    public function getMessages(): array;
}

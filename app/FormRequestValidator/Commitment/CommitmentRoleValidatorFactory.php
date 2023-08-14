<?php

namespace App\FormRequestValidator\Commitment;

class CommitmentRoleValidatorFactory
{

    public static function make($roleId, $inputData): CommitmentRoleValidatorInterface
    {
        switch ($roleId) {
            case 1:
                return new SecCGFRoleValidator();
            case 2:
                return new SecJAPCRoleValidator();
            case 3:
                return new AnalystRoleValidator($inputData);
            case 4:
                return new CoordCGFRoleValidator();
                // Agregar más casos para otros roles
            default:
                throw new \InvalidArgumentException("Invalid role ID");
        }
    }
}

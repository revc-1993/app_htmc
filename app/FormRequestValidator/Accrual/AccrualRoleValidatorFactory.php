<?php

namespace App\FormRequestValidator\Accrual;

use App\Constants\ManagementRoles;

class AccrualRoleValidatorFactory
{
    public static function make($roleId, $inputData, $currentRecordId): AccrualRoleValidatorInterface
    {
        switch ($roleId) {
            case ManagementRoles::SEC_CGF:
                return new SecCGFRoleValidator();
            case ManagementRoles::SEC_JAPC:
                return new SecJAPCRoleValidator();
            case ManagementRoles::ANALYST:
                return new AnalystRoleValidator($inputData, $currentRecordId);
            case ManagementRoles::COORD_CGF:
                return new CoordCGFRoleValidator();
            case ManagementRoles::ADMIN:
                return new AdminRoleValidator($inputData, $currentRecordId);
                // Agregar más casos para otros roles
            default:
                throw new \InvalidArgumentException("Invalid role ID");
        }
    }
}

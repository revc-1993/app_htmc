<?php

namespace App\FormRequestValidator\Payment;

use App\Constants\LaborManagements;
use App\Constants\ManagementRoles;

class PaymentRoleValidatorFactory
{
    public static function make($roleId, $inputData, $currentRecordId): PaymentRoleValidatorInterface
    {
        switch ($roleId) {
            case LaborManagements::STEP_1:
                return new TreasuryManagerRoleValidator();
            case LaborManagements::STEP_2:
                return new TreasuryAnalystRoleValidator($inputData, $currentRecordId);
            case LaborManagements::STEP_5:
                return new AdminRoleValidator($inputData, $currentRecordId);
            default:
                throw new \InvalidArgumentException("Invalid role ID");
        }
    }
}

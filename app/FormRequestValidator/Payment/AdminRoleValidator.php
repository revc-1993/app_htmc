<?php

namespace App\FormRequestValidator\Payment;

use App\Constants\Modules;
use App\Constants\ExpenseTypes;
use App\Constants\ManagementRoles;
use App\Rules\UniqueNumberCurInYear;
use App\Rules\ValidateAccrualAmount;
use App\Rules\CustomRecordStatusRule;
use App\Rules\ValidateCommitmentAmount;
use App\Constants\ManagementRecordStatus;

class AdminRoleValidator implements PaymentRoleValidatorInterface
{
    private $inputData;
    private $currentRecordId;

    public function __construct(array $inputData, $currentRecordId)
    {
        $this->inputData = $inputData;
        $this->currentRecordId = $currentRecordId;
    }

    private function getBaseRules(): array
    {
        return [
            'customer_id' => ['required'],
            'manager_comments' => ['nullable'],
            'treasury_approved' => ['nullable'],
        ];
    }

    private function getAnalystRules(): array
    {
        return [
            'analyst_status' => ['required'],
            'manager_comments' => ['nullable'],
        ];
    }

    public function getRules(): array
    {
        $rules = $this->getBaseRules();

        $currentManagement = $this->inputData['current_management'];

        if ($currentManagement === ManagementRoles::SEC_CGF) {
            return $rules;
        }

        if ($currentManagement === ManagementRoles::SEC_JAPC) {
            return array_merge_recursive($rules, $this->getAnalystRules());
        }

        return $rules;
    }

    public function getAttributes(): array
    {
        return [
            'customer_id' => "Usuario",
            'manager_comments' => "Observaciones",
            'treasury_approved' => "Estado de aprobación",
            'analyst_status' => "Estado de Pago",
            'payment_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [];
    }
}

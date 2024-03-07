<?php

namespace App\FormRequestValidator\Payment;

use App\Constants\Modules;
use App\Rules\UniqueNumberCurInYear;
use App\Rules\ValidateAccrualAmount;
use App\Rules\CustomRecordStatusRule;

class TreasuryAnalystRoleValidator implements PaymentRoleValidatorInterface
{
    private $inputData;
    private $currentRecordId;

    public function __construct(array $inputData, $currentRecordId)
    {
        $this->inputData = $inputData;
        $this->currentRecordId = $currentRecordId;
    }

    public function getRules(): array
    {
        return [
            'analyst_status' => ['required'],
            'manager_comments' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'analyst_status' => "Estado de Pago",
            'payment_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [];
    }
}

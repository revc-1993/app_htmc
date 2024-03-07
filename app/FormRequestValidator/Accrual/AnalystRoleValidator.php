<?php

namespace App\FormRequestValidator\Accrual;

use App\Constants\Modules;
use App\Rules\UniqueNumberCurInYear;
use App\Rules\ValidateAccrualAmount;
use App\Rules\CustomRecordStatusRule;

class AnalystRoleValidator implements AccrualRoleValidatorInterface
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
        $commitmentId = $this->inputData['commitment_id'];
        $currentManagement = $this->inputData['current_management'];
        $treasuryApproved = $this->inputData['treasury_approved'];

        return [
            'commitment_id' => ['required'],
            'accrual_amount' => [
                'nullable',
                'min:0',
                'required_if:record_status_id,5',
                new ValidateAccrualAmount($commitmentId, $currentManagement, $treasuryApproved)
            ],
            'record_status_id' => [new CustomRecordStatusRule($currentManagement, $treasuryApproved)],
            'accrual_cur' => [
                new UniqueNumberCurInYear(Modules::ACCRUALS, $this->currentRecordId),
                'required_if:record_status_id,5',
                'numeric',
                'min:1',
                'nullable'
            ],
            'accrual_comments' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'commitment_id' => "Compromiso",
            'accrual_amount' => "Monto Devengado",
            'record_status_id' => "Estado de Devengado",
            'accrual_cur' => "CUR de Devengado",
            'accrual_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [
            'accrual_amount.required_if' => "El campo :attribute es requerido",
            'accrual_cur.required_if' => "El campo :attribute es requerido",
        ];
    }
}

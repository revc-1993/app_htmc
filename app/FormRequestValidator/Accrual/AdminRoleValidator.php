<?php

namespace App\FormRequestValidator\Accrual;

use App\Constants\Modules;
use App\Constants\ExpenseTypes;
use App\Constants\ManagementRoles;
use App\Rules\UniqueNumberCurInYear;
use App\Rules\ValidateAccrualAmount;
use App\Rules\CustomRecordStatusRule;
use App\Rules\ValidateCommitmentAmount;
use App\Constants\ManagementRecordStatus;

class AdminRoleValidator implements AccrualRoleValidatorInterface
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
            'accrual_memo' => [
                'nullable',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
            ],
            'voucher_number' => ['nullable', 'regex:/^[A-Za-z0-9 _-]+$/', 'string', 'min:15', 'max:100'],
            'sec_cgf_comments' => ['nullable'],
            'current_management' => ['nullable'],
        ];
    }

    private function getSecJAPCRules(): array
    {
        return [
            'accrual_memo' => [
                'nullable',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
            ],
            'customer_id' => ['required'],
            'japc_comments' => ['nullable'],
        ];
    }

    private function getAnalystRules(): array
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

    public function getRules(): array
    {
        $rules = $this->getBaseRules();

        $currentManagement = $this->inputData['current_management'];
        $approvedState = $this->inputData['treasury_approved'];

        if ($currentManagement === ManagementRoles::SEC_CGF) {
            return $rules;
        }

        if ($currentManagement === ManagementRoles::SEC_JAPC) {
            return array_merge_recursive($rules, $this->getSecJAPCRules());
        }

        if ($currentManagement === ManagementRoles::ANALYST && is_null($approvedState)) {
            return array_merge_recursive($rules, $this->getSecJAPCRules(), $this->getAnalystRules());
        }

        if ($currentManagement === ManagementRoles::COORD_CGF || !is_null($approvedState)) {
            return array_merge_recursive($rules, $this->getSecJAPCRules(), $this->getAnalystRules(), $this->getCoordCGFRules());
        }

        return $rules;
    }

    private function getCoordCGFRules(): array
    {
        return [
            'treasury_approved' => ['required'],
            'returned_document_number' => [
                'required',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/',
            ],
            'coord_cgf_comments' => ['nullable'],
        ];
    }

    public function getAttributes(): array
    {
        return [
            'accrual_memo' => "Nro. de Memorando",
            'voucher_number' => "Número de Comprobante",
            'sec_cgf_comments' => "Observaciones",
            'customer_id' => "Usuario",
            'japc_comments' => "Observaciones",
            'commitment_id' => "Compromiso",
            'accrual_amount' => "Monto Devengado",
            'record_status_id' => "Estado de Devengado",
            'accrual_cur' => "CUR de Devengado",
            'accrual_comments' => "Observaciones",
            'treasury_approved' => "Estado Final de Expediente",
            'returned_document_number' => "Nro. de Memorando",
            'coord_cgf_comments' => "Observaciones",
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

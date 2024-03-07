<?php

namespace App\FormRequestValidator\Commitment;

use App\Constants\Modules;
use App\Constants\ExpenseTypes;
use App\Constants\ManagementRoles;
use App\Rules\UniqueNumberCurInYear;
use App\Rules\CustomRecordStatusRule;
use App\Rules\ValidateCommitmentAmount;
use App\Constants\ManagementRecordStatus;

class AdminRoleValidator implements CommitmentRoleValidatorInterface
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
            'commitment_memo' => [
                'nullable',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
            ],
            'process_number' => ['nullable', 'alphadash', 'string', 'min:15', 'max:100'],
            'contract_administrator_id' => ['required'],
            'contract_number' => ['required'],
            'purchase_order' => ['nullable'],
            'sec_cgf_comments' => ['nullable'],
            'current_management' => ['nullable'],
        ];
    }

    private function getSecJAPCRules(): array
    {
        return [
            'process_number' => ['nullable', 'alphadash', 'string', 'min:15', 'max:100'],
            'customer_id' => ['required'],
            'japc_comments' => ['nullable'],
        ];
    }

    private function getAnalystRules(): array
    {
        $certificationId = $this->inputData['certification_id'];
        $currentManagement = $this->inputData['current_management'];
        $treasuryApproved = $this->inputData['treasury_approved'];

        return [
            'certification_id' => ['required'],
            'vendor_id' => ['required'],
            'commitment_amount' => [
                'nullable',
                'min:0',
                'required_if:record_status_id,5',
                new ValidateCommitmentAmount($certificationId, $currentManagement, $treasuryApproved)
            ],
            'record_status_id' => [new CustomRecordStatusRule($currentManagement, $treasuryApproved)],
            'commitment_cur' => [
                new UniqueNumberCurInYear(Modules::COMMITMENTS, $this->currentRecordId),
                'required_if:record_status_id,5',
                'numeric',
                'min:1',
                'nullable'
            ],
            'commitment_comments' => ['nullable'],
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
            'commitment_memo' => "Nro. de Memorando",
            'process_number' => "Número de Proceso",
            'contract_administrator_id' => "Administrador de Contrato - Orden de Compra",
            'contract_number' => "Número de Contrato - Convenio Marco",
            'purchase_order' => "Orden de Compra",
            'sec_cgf_comments' => "Observaciones",
            'customer_id' => "Usuario",
            'japc_comments' => "Observaciones",
            'certification_id' => "Certificación",
            'vendor_id' => "Proveedor",
            'commitment_amount' => "Monto Comprometido",
            'record_status_id' => "Estado de Compromiso",
            'commitment_cur' => "CUR de Compromiso",
            'commitment_comments' => "Observaciones",
            'treasury_approved' => "Estado Final de Expediente",
            'returned_document_number' => "Nro. de Memorando",
            'coord_cgf_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [
            'commitment_amount.required_if' => "El campo :attribute es requerido",
            'commitment_cur.required_if' => "El campo :attribute es requerido",
        ];
    }
}

<?php

namespace App\FormRequestValidator\Certification;

use App\Constants\Modules;
use App\Constants\ExpenseTypes;
use App\Constants\ManagementRoles;
use App\Rules\UniqueNumberCurInYear;

class AdminRoleValidator implements CertificationRoleValidatorInterface
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
            'certification_memo' => [
                'nullable',
                'regex: /^[A-Z]{4}-[A-Z]{4}-[A-Z]{1,10}-[0-9]{1,4}-[0-9]{2,6}-[MO]$/'
            ],
            'content' => ['nullable', 'min:8'],
            'contract_object' => ['required', 'string', 'min:15', 'max:255'],
            'process_type_id' => ['required'],
            'expense_type_id' => ['required'],
            'department_id' => ['required'],
            'sec_cgf_comments' => ['nullable'],
            'current_management' => ['nullable'],
        ];
    }

    private function getSecJAPCRules(): array
    {
        return [
            'content' => ['nullable', 'min:10'],
            'customer_id' => ['required'],
            'japc_comments' => ['nullable'],
        ];
    }

    private function getAnalystRules(): array
    {
        return [
            // 'vendor_id' => ($this->inputData['expense_type_id'] === ExpenseTypes::NEW_PROCESS ? ['nullable'] : ['required']),
            'budget_line_id' => ['required'],
            'certified_amount' => ['nullable', 'numeric', 'min:0', 'required_if:record_status_id,5'],
            'certification_number' => [
                new UniqueNumberCurInYear(Modules::CERTIFICATIONS, $this->currentRecordId),
                'required_if:record_status_id,5',
                'numeric',
                'min:1',
                'nullable'
            ],
            'record_status_id' => ['required', 'in:1,2,3,4,5,6'],
            'certification_comments' => ['nullable'],
        ];
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
            // dd($approvedState);
            return array_merge_recursive($rules, $this->getSecJAPCRules(), $this->getAnalystRules(), $this->getCoordCGFRules());
        }

        return $rules;
    }

    public function getAttributes(): array
    {
        return [
            'certification_memo' => "Nro. de Memorando",
            'content' => "Contenido de Expediente",
            'contact_object' => "Objeto de Contrato",
            'procces_type_id' => "Tipo de Proceso",
            'expense_type_id' => "Tipo de Gasto",
            'department_id' => "Area",
            'sec_cgf_comments' => "Observaciones",
            'customer_id' => "Usuario",
            'japc_comments' => "Observaciones",
            'budget_line_id' => "Ítem Presupuestario",
            'certified_amount' => "Monto Certificado",
            'certification_number' => "Nro. de Certificación",
            'record_status_id' => "Estado de Certificación",
            'certification_comments' => "Observaciones",
            'treasury_approved' => "Estado Final de Expediente",
            'returned_document_number' => "Nro. de Memorando",
            'coord_cgf_comments' => "Observaciones",
        ];
    }

    public function getMessages(): array
    {
        return [
            'certified_amount.required_if' => "El campo :attribute es requerido",
            'certification_number.required_if' => "El campo :attribute es requerido",
        ];
    }
}

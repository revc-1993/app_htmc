<?php

namespace App\FormRequestValidator\Commitment;

use App\Constants\Modules;
use App\Rules\UniqueNumberCurInYear;
use App\Rules\CustomRecordStatusRule;
use App\Rules\ValidateCommitmentAmount;

class AnalystRoleValidator implements CommitmentRoleValidatorInterface
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

    public function getAttributes(): array
    {
        return [
            'certification_id' => "Certificación",
            'vendor_id' => "Proveedor",
            'commitment_amount' => "Monto Comprometido",
            'record_status_id' => "Estado de Compromiso",
            'commitment_cur' => "CUR de Compromiso",
            'commitment_comments' => "Observaciones",
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

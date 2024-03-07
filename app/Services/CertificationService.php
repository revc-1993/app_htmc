<?php

namespace App\Services;

use App\Models\Certification;
use App\Http\Requests\StoreCertificationRequest;

use App\Constants\ManagementRoles;
use App\Constants\ManagementRecordStatus;
use App\Models\CertificationBudgetLine;

class CertificationService
{
    public function getAllCertifications()
    {
        return Certification::with([
            'user',
            'currentManagement',
            'department',
            'processType',
            'expenseType',
            'certificationBudgetLines',
            'recordStatus'
        ])
            ->filtered()
            ->get();
    }

    public function getCertificationForEdit(int $id)
    {
        return Certification::with([
            'certificationBudgetLines' => function ($query) {
                $query->select('id', 'certification_id', 'budget_line_id');
            }
        ])
            ->where('id', $id)
            ->firstOrFail();
    }

    public function createCertification(StoreCertificationRequest $request)
    {
        $adjustedRequest = $this->adjustParams($request);
        return Certification::create($adjustedRequest);
    }

    public function updateCertification(Certification $certification, array $adjustedRequest)
    {
        // Certification
        $certification->update($adjustedRequest);

        // CertificationBudgetLine
        if (array_key_exists('budget_line_id', $adjustedRequest)) {
            $requestBudgetLines = $adjustedRequest['budget_line_id'];
            $budgetLines = [];
            foreach ($requestBudgetLines as $budgetLine) {
                $budgetLines[] = new CertificationBudgetLine([
                    'certification_id' => $certification['id'],
                    'budget_line_id' => $budgetLine,
                ]);
            }
            $certification->certificationBudgetLines()->delete();
            $certification->certificationBudgetLines()->saveMany($budgetLines);
        }
    }

    private function getCertificationBalance($request)
    {
        if ($request['treasury_approved'] === "approved") {
            return $request['certified_amount'];
        }

        return null;
    }

    public function adjustParams($request)
    {
        $adjustedRequest = $request->validated(); // Obtener los datos validados del request

        $currentManagement = $request['current_management'] ?? null;

        // dd($currentManagement);

        // Agregar fecha dependiendo del estado actual de la certificación
        $dates = ['sec_cgf_date', 'assignment_date', 'cp_date', 'coord_cgf_date'];
        $adjustedRequest[$dates[$currentManagement - 1]] = now();

        // Asignar la gestión siguiente dependiendo de la actual
        // dd($adjustedRequest['current_management']);
        $adjustedRequest['current_management'] = $this->manageAssignment($request);
        // dd($adjustedRequest['current_management']);

        // Asignar saldo si el monto certificado no es nulo y el saldo no está definido, y está aprobado. ó cero si no lo está
        $adjustedRequest['balance'] = $this->getCertificationBalance($request);

        // Manejar el estado de la certificación
        $adjustedRequest['record_status_id'] = $this->manageRecordStatus($request);

        return $adjustedRequest;
    }

    protected function manageAssignment($adjustedRequest)
    {
        $currentManagement = $adjustedRequest['current_management'];
        $contractObject  = $adjustedRequest['contract_object'];
        $customerId  = $adjustedRequest['customer_id'];
        $recordStatusId  = $adjustedRequest['record_status_id'];
        $treasuryApproved  = $adjustedRequest['treasury_approved'];

        if (
            !is_null($contractObject) && is_null($customerId)
            && is_null($recordStatusId) && is_null($treasuryApproved)
        ) {
            // dd("PASA A CURRENT 2");
            return ManagementRoles::SEC_JAPC;
        }

        if (
            !is_null($contractObject) && !is_null($customerId)
            && (
                // 1. Estado de certificación vacío y Check de Coord. vacío
                (is_null($recordStatusId) && is_null($treasuryApproved))
                // 2. Estados antes de registrados (en revisión, devuelto); y con estado del Coord. "Returned"
                || ($recordStatusId < ManagementRecordStatus::REGISTERED
                    && (is_null($treasuryApproved) || $treasuryApproved === "returned"
                    )
                )
                // 3. Estado aprobado y aprobación "Returned"
                || (
                    ($recordStatusId > ManagementRecordStatus::REGISTERED || is_null($recordStatusId))
                    && $treasuryApproved === "returned"
                )
                // 4. Cuando estado es registrado, aprobación del Coord es "Returned" y gestión actual es Coord. 
                || ($recordStatusId === ManagementRecordStatus::REGISTERED && $treasuryApproved === "returned" && $currentManagement === ManagementRoles::COORD_CGF)
            )
        ) {
            // dd("PASA A CURRENT 3");
            return ManagementRoles::ANALYST;
        }

        if (
            !is_null($contractObject) && !is_null($customerId)
            && (
                // 1. Estado es registrado, y approved es nulo (primera vez)
                ($recordStatusId === ManagementRecordStatus::REGISTERED && is_null($treasuryApproved))
                // 2. Estado es aprobado o liquidado, y 
                || (
                    ($recordStatusId >= ManagementRecordStatus::REGISTERED || is_null($recordStatusId))
                    && $treasuryApproved !== "returned"
                )
                // 3. Si estado es Registrado, gestión actual es 3 y aprobación coord previa es devuelto  
                || ($recordStatusId === ManagementRecordStatus::REGISTERED && $treasuryApproved === "returned" && $currentManagement === ManagementRoles::ANALYST)
                // 4. Si estado es Devuelto, gestión actual es 3 y aprobación coord previa es aprobado o liquidado  
                || ($recordStatusId === ManagementRecordStatus::RETURNED && $treasuryApproved !== "returned" && $currentManagement === ManagementRoles::ANALYST)
            )
        ) {
            // dd("PASA A CURRENT 4");
            return ManagementRoles::COORD_CGF;
        }

        // Default
        // return $currentManagement;
    }

    protected function manageRecordStatus($adjustedRequest)
    {
        $recordStatusId = $adjustedRequest['record_status_id'];
        $treasuryApproved = $adjustedRequest['treasury_approved'];
        $currentManagement = $adjustedRequest['current_management'];

        if (
            // 1. Cuando previamente está aprobado
            (($recordStatusId > ManagementRecordStatus::REGISTERED || is_null($recordStatusId)) && $treasuryApproved === "returned")
            // 2. 
            || ($recordStatusId === ManagementRecordStatus::REGISTERED && $treasuryApproved === "returned" && $currentManagement === ManagementRoles::COORD_CGF)
        ) {
            return ManagementRecordStatus::RETURNED;
        }

        if (
            // 1
            (($recordStatusId >= ManagementRecordStatus::REGISTERED || is_null($recordStatusId)) && $treasuryApproved === "approved")
            || ($recordStatusId === ManagementRecordStatus::RETURNED && $treasuryApproved === "approved" && $currentManagement === ManagementRoles::ANALYST)
        ) {
            return ManagementRecordStatus::APPROVED;
        }

        if (
            // 1
            (($recordStatusId >= ManagementRecordStatus::REGISTERED || is_null($recordStatusId)) && $treasuryApproved === "canceled")
            || ($recordStatusId === ManagementRecordStatus::RETURNED && $treasuryApproved === "canceled" && $currentManagement === ManagementRoles::ANALYST)
        ) {
            return ManagementRecordStatus::CANCELED;
        }

        if (
            // 1
            (($recordStatusId >= ManagementRecordStatus::REGISTERED || is_null($recordStatusId)) && $treasuryApproved === "liquidated")
            || ($recordStatusId === ManagementRecordStatus::RETURNED && $treasuryApproved === "liquidated" && $currentManagement === ManagementRoles::ANALYST)
        ) {
            return ManagementRecordStatus::LIQUIDATED;
        }

        return $recordStatusId;
    }

    public function getCertificationByNumber($certificationNumber)
    {

        // dd($certificationNumber);
        return Certification::approved($certificationNumber)
            ->first(['id', 'certification_number', 'contract_object', 'balance']);
    }

    function getRole()
    {
        return auth()->user()->roles->first()->id;
    }
}

<?php

namespace App\Services;

use App\Models\Certification;
use App\Http\Requests\StoreCertificationRequest;

class CertificationService
{
    public function getAllCertifications()
    {
        return Certification::with([
            'user', 'department', 'processType', 'expenseType', 'budgetLine', 'recordStatus'
        ])
            ->filtered()
            ->get();
    }

    public function getCertificationForEdit($id)
    {
        return Certification::with([
            'vendor' => function ($query) {
                $query->select('id', 'nit', 'name');
            }
        ])->where('id', $id)->firstOrFail();
    }

    public function createCertification(StoreCertificationRequest $request)
    {
        $adjustedRequest = $this->adjustParams($request);
        return Certification::create($adjustedRequest);
    }

    public function updateCertification(Certification $certification, array $adjustedRequest)
    {
        $certification->update($adjustedRequest);
    }

    public function adjustParams($request)
    {
        $adjustedRequest = $request->validated(); // Obtener los datos validados del request

        $currentManagement = $request['current_management'] ?? null;

        // Agregar fecha dependiendo del estado actual de la certificación
        $dates = ['sec_cgf_date', 'assignment_date', 'cp_date', 'coord_cgf_date'];
        $adjustedRequest[$dates[$currentManagement - 1]] = now();

        // Asignar saldo si el monto certificado no es nulo y el saldo no está definido
        if (!isset($adjustedRequest['balance']) && isset($adjustedRequest['certified_amount'])) {
            $adjustedRequest['balance'] = $adjustedRequest['certified_amount'];
        }

        // Asignar la gestión siguiente dependiendo de la actual
        $adjustedRequest['current_management'] = $this->manageAssignment($request);
        // dd($adjustedRequest);

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
        )
            return 2;

        if (
            !is_null($contractObject) && !is_null($customerId)
            && (
                // 1. Estado de certificación vacío y Check de Coord. vacío
                (is_null($recordStatusId) && is_null($treasuryApproved))
                // 2. Estados antes de registrados (en revisión, devuelto); y con estado del Coord. "Returned"
                || ($recordStatusId < 5
                    && (is_null($treasuryApproved) || $treasuryApproved === "returned"
                    )
                )
                // 3. Estado aprobado y aprobación "Returned"
                || (
                    ($recordStatusId > 5 || is_null($recordStatusId))
                    && $treasuryApproved === "returned"
                )
                // 4. Cuando estado es registrado, aprobación del Coord es "Returned" y gestión actual es Coord. 
                || ($recordStatusId === 5 && $treasuryApproved === "returned" && $currentManagement === 4)
            )
        )
            return 3;

        if (
            !is_null($contractObject) && !is_null($customerId)
            && (
                // 1. Estado es registrado, y approved es nulo (primera vez)
                ($recordStatusId === 5 && is_null($treasuryApproved))
                // 2. Estado es aprobado o liquidado, y 
                || (
                    ($recordStatusId >= 5 || is_null($recordStatusId))
                    && $treasuryApproved !== "returned"
                )
                // 3. Si estado es Registrado, gestión actual es 3 y aprobación coord previa es devuelto  
                || ($recordStatusId === 5 && $treasuryApproved === "returned" && $currentManagement === 3)
                // 4. Si estado es Devuelto, gestión actual es 3 y aprobación coord previa es aprobado o liquidado  
                || ($recordStatusId === 4 && $treasuryApproved !== "returned" && $currentManagement === 3)
            )
        )
            return 4;

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
            (($recordStatusId > 5 || is_null($recordStatusId)) && $treasuryApproved === "returned")
            // 2. 
            || ($recordStatusId === 5 && $treasuryApproved === "returned" && $currentManagement === 4)
        ) {
            return 4;
        }

        if (
            // 1
            (($recordStatusId >= 5 || is_null($recordStatusId)) && $treasuryApproved === "approved")
            || ($recordStatusId === 4 && $treasuryApproved === "approved" && $currentManagement === 3)
        ) {
            return 6;
        }

        if (
            // 1
            (($recordStatusId >= 5 || is_null($recordStatusId)) && $treasuryApproved === "canceled")
            || ($recordStatusId === 4 && $treasuryApproved === "canceled" && $currentManagement === 3)
        ) {
            return 7;
        }

        if (
            // 1
            (($recordStatusId >= 5 || is_null($recordStatusId)) && $treasuryApproved === "liquidated")
            || ($recordStatusId === 4 && $treasuryApproved === "liquidated" && $currentManagement === 3)
        ) {
            return 8;
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
        return auth()->user()->roles()->first()->id;
    }
}

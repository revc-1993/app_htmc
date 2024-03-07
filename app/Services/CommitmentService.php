<?php

namespace App\Services;

use App\Http\Requests\StoreCommitmentRequest;
use App\Models\Commitment;

use App\Constants\ManagementRoles;
use App\Constants\ManagementRecordStatus;
use App\Models\Certification;

class CommitmentService
{
    public function getAllCommitments()
    {
        return Commitment::with([
            'certification' => function ($query) {
                $query->select('id', 'certification_number', 'contract_object', 'balance');
            },
            'vendor',
            'contractAdministrator',
            'user',
            'currentManagement',
            'recordStatus',
        ])
            ->filtered()
            ->get();
    }

    public function getCommitmentForEdit(int $id)
    {
        return Commitment::with([
            'certification' => function ($query) {
                $query->select('id', 'certification_number', 'contract_object', 'balance');
            },
            'vendor' => function ($query) {
                $query->select('id', 'nit', 'name');
            },
            'contractAdministrator' => function ($query) {
                $query->select('id', 'ci', 'names');
            }
        ])
            ->where('id', $id)
            ->firstOrFail();
    }

    public function createCommitment(StoreCommitmentRequest $request)
    {
        $adjustedRequest = $this->adjustParams($request);
        return Commitment::create($adjustedRequest);
    }

    public function updateCommitment(Commitment $commitment, array $adjustedRequest)
    {
        $this->handleLinkedCertification($commitment, $adjustedRequest);
        $commitment->update($adjustedRequest);
    }

    public function deleteCommitment(Commitment $commitment)
    {
        if ($commitment->record_status_id === ManagementRecordStatus::APPROVED) {
            // Obtener el monto del compromiso del array ajustado
            $commitmentAmount = $commitment->commitment_amount;
            // Actualizar la certificación relacionada restando el monto del balance
            $certification = $commitment->certification;

            $this->updateCertificationBalance($certification, $commitmentAmount);
        }
        $commitment->delete();
    }

    private function handleLinkedCertification(Commitment $commitment, array $adjustedRequest)
    {
        // VALIDACION 1: EN EL UPDATE, NO HAGA EFECTO, SI VUELVE A ESCOGER LA MISMA OPCION
        // TRUE: CON CAMBIOS - PERMITE CAMBIAR. FALSE: SIN CAMBIOS - NO PERMITE CAMBIAR
        $recordStatusChange = $commitment->record_status_id !== $adjustedRequest['record_status_id'];

        // VALIDACION 2: CONDICIONES PARA HACER SUMA DE SALDO
        // Condición: Si pasa de 6-Aprobado a 4-Devuelto o a 7-Cancelado
        $conditionsRevertBalance = $commitment->record_status_id === ManagementRecordStatus::APPROVED
            && ($adjustedRequest['record_status_id'] === ManagementRecordStatus::RETURNED
                || $adjustedRequest['record_status_id'] === ManagementRecordStatus::CANCELED);

        // VALIDACION 3: CONDICIONES PARA HACER DEBITO DE SALDO
        // Condición: Si, estando en estados 4-Devuelto, 5-Registrado o 7-Cancelado, pasa a estado 6-Aprobado
        $conditionsSubtractBalance = ($commitment->record_status_id === ManagementRecordStatus::RETURNED
            || $commitment->record_status_id === ManagementRecordStatus::REGISTERED
            || $commitment->record_status_id === ManagementRecordStatus::CANCELED)
            && $adjustedRequest['record_status_id'] === ManagementRecordStatus::APPROVED;

        if ($recordStatusChange) {
            // Obtener el monto del compromiso del array ajustado
            $commitmentAmount = $commitment->commitment_amount;
            // Certificación ligada
            $certification = $commitment->certification;

            if ($recordStatusChange && $conditionsSubtractBalance) {
                // Actualizar la certificación relacionada restando el monto del balance
                return $this->updateCertificationBalance($certification, -$commitmentAmount);
            }

            if ($recordStatusChange && $conditionsRevertBalance) {
                // Actualizar la certificación relacionada sumando el monto del balance
                return $this->updateCertificationBalance($certification, $commitmentAmount);
            }
        }
    }

    private function updateCertificationBalance(Certification $certification, $amount)
    {
        $certification->balance += $amount;
        $certification->save();
    }

    private function getCommitmentBalance($request)
    {
        if ($request['treasury_approved'] === "approved") {
            return $request['commitment_amount'];
        }

        return null;
    }

    public function adjustParams($request)
    {
        $adjustedRequest = $request->validated();

        $currentManagement = $request['current_management'] ?? null;

        // Agregar fecha dependiendo del estado actual del compromiso
        $dates = ['sec_cgf_date', 'assignment_date', 'commitment_date', 'coord_cgf_date'];
        $adjustedRequest[$dates[$currentManagement - 1]] = now();

        // Asignar la gestión siguiente dependiendo de la actual
        $adjustedRequest['current_management'] = $this->manageAssignment($request);

        // Asignar saldo si el monto comprometido no es nulo y el saldo no está definido, y está aprobado. ó cero si no lo está
        $adjustedRequest['balance'] = $this->getCommitmentBalance($request);

        // Manejar el estado del compromiso
        $adjustedRequest['record_status_id'] = $this->manageRecordStatus($request);

        return $adjustedRequest;
    }

    protected function manageAssignment($adjustedRequest)
    {
        $currentManagement = $adjustedRequest['current_management'];
        $contractAdministrator  = $adjustedRequest['contract_administrator_id'];
        $customerId  = $adjustedRequest['customer_id'];
        $recordStatusId  = $adjustedRequest['record_status_id'];
        $treasuryApproved  = $adjustedRequest['treasury_approved'];

        if (
            !is_null($contractAdministrator) && is_null($customerId)
            && is_null($recordStatusId) && is_null($treasuryApproved)
        )
            return ManagementRoles::SEC_JAPC;

        if (
            !is_null($contractAdministrator) && !is_null($customerId)
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
                || ($recordStatusId === ManagementRecordStatus::REGISTERED
                    && $treasuryApproved === "returned"
                    && $currentManagement === ManagementRoles::COORD_CGF)
            )
        )
            return ManagementRoles::ANALYST;

        if (
            !is_null($contractAdministrator) && !is_null($customerId)
            && (
                // 1. Estado es registrado, y approved es nulo (primera vez)
                ($recordStatusId === ManagementRecordStatus::REGISTERED && is_null($treasuryApproved))
                // 2. Estado es aprobado o liquidado, y 
                || (
                    ($recordStatusId >= ManagementRecordStatus::REGISTERED || is_null($recordStatusId))
                    && $treasuryApproved !== "returned"
                )
                // 3. Si estado es Registrado, gestión actual es 3 y aprobación coord previa es devuelto  
                || ($recordStatusId === ManagementRecordStatus::REGISTERED
                    && $treasuryApproved === "returned"
                    && $currentManagement === ManagementRoles::ANALYST)
                // 4. Si estado es Devuelto, gestión actual es 3 y aprobación coord previa es aprobado o liquidado  
                || ($recordStatusId === ManagementRecordStatus::RETURNED
                    && $treasuryApproved !== "returned"
                    && $currentManagement === ManagementRoles::ANALYST)
            )
        )
            return ManagementRoles::COORD_CGF;
    }

    protected function manageRecordStatus($adjustedRequest)
    {
        $recordStatusId = $adjustedRequest['record_status_id'];
        $treasuryApproved = $adjustedRequest['treasury_approved'];
        $currentManagement = $adjustedRequest['current_management'];

        if (
            // 1. Cuando previamente está aprobado
            (($recordStatusId > ManagementRecordStatus::REGISTERED
                || is_null($recordStatusId))
                && $treasuryApproved === "returned")
            // 2. 
            || ($recordStatusId === ManagementRecordStatus::REGISTERED
                && $treasuryApproved === "returned"
                && $currentManagement === ManagementRoles::COORD_CGF)
        ) {
            return ManagementRecordStatus::RETURNED;
        }

        if (
            // 1
            (($recordStatusId >= ManagementRecordStatus::REGISTERED
                || is_null($recordStatusId))
                && $treasuryApproved === "approved")
            || ($recordStatusId === ManagementRecordStatus::RETURNED
                && $treasuryApproved === "approved"
                && $currentManagement === ManagementRoles::ANALYST)
        ) {
            return ManagementRecordStatus::APPROVED;
        }

        if (
            // 1
            (($recordStatusId >= ManagementRecordStatus::REGISTERED
                || is_null($recordStatusId))
                && $treasuryApproved === "canceled")
            || ($recordStatusId === ManagementRecordStatus::RETURNED
                && $treasuryApproved === "canceled"
                && $currentManagement === ManagementRoles::ANALYST)
        ) {
            return ManagementRecordStatus::CANCELED;
        }

        if (
            // 1
            (($recordStatusId >= ManagementRecordStatus::REGISTERED
                || is_null($recordStatusId))
                && $treasuryApproved === "liquidated")
            || ($recordStatusId === ManagementRecordStatus::RETURNED
                && $treasuryApproved === "liquidated"
                && $currentManagement === ManagementRoles::ANALYST)
        ) {
            return ManagementRecordStatus::LIQUIDATED;
        }

        return $recordStatusId;
    }

    public function getCommitmentByNumber($commitmentNumber)
    {
        // dd($certificationNumber);
        return Commitment::approved($commitmentNumber)
            ->with('contractAdministrator', 'vendor')
            ->first();
    }

    function getRole()
    {
        return auth()->user()->roles->first()->id;
    }
}

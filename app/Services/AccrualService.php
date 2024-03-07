<?php

namespace App\Services;

use App\Constants\LaborManagements;
use App\Models\Accrual;
use App\Models\Commitment;

use App\Constants\ManagementRoles;
use App\Constants\ManagementRecordStatus;
use App\Http\Requests\StoreAccrualRequest;
use App\Models\Payment;

class AccrualService
{
    public function getAllAccruals()
    {
        return Accrual::with([
            'commitment',
            'commitment.vendor',
            'commitment.contractAdministrator',
            'payment.managerStatus',
            'user',
            'currentManagement',
            'recordStatus',

        ])->get();
    }

    public function getAccrualForEdit(int $id)
    {
        return Accrual::with([
            'commitment.contractAdministrator',
            'commitment.vendor'
        ])
            ->where('id', $id)
            ->firstOrFail();
    }

    public function createAccrual(array $adjustedRequest)
    {
        return Accrual::create($adjustedRequest);
    }

    public function updateAccrual(Accrual $accrual, array $adjustedRequest)
    {
        $this->handleLinkedCommitment($accrual, $adjustedRequest);
        $accrual->update($adjustedRequest);
    }

    public function deleteAccrual(Accrual $accrual)
    {
        if ($accrual->record_status_id === ManagementRecordStatus::APPROVED) {
            // Obtener el monto del devengado del array ajustado
            $accrualAmount = $accrual->accrual_amount;
            // Compromiso ligado
            $commitment = $accrual->commitment;

            $this->updateCommitmentBalance($commitment, $accrualAmount);
        }
        $accrual->delete();
    }

    private function handleLinkedCommitment(Accrual $accrual, array $adjustedRequest)
    {
        // VALIDACION 1: EN EL UPDATE, NO HAGA EFECTO, SI VUELVE A ESCOGER LA MISMA OPCION
        // TRUE: CON CAMBIOS - PERMITE CAMBIAR. FALSE: SIN CAMBIOS - NO PERMITE CAMBIAR
        $recordStatusChange = $accrual->record_status_id !== $adjustedRequest['record_status_id'];

        // VALIDACION 2: CONDICIONES PARA HACER SUMA DE SALDO
        // Condición: Si pasa de 6-Aprobado a 4-Devuelto o a 7-Cancelado
        $conditionsRevertBalance = $accrual->record_status_id === ManagementRecordStatus::APPROVED
            && ($adjustedRequest['record_status_id'] === ManagementRecordStatus::RETURNED
                || $adjustedRequest['record_status_id'] === ManagementRecordStatus::CANCELED);

        // VALIDACION 3: CONDICIONES PARA HACER DEBITO DE SALDO
        // Condición: Si, estando en estados 4-Devuelto, 5-Registrado o 7-Cancelado, pasa a estado 6-Aprobado
        $conditionsSubtractBalance = ($accrual->record_status_id === ManagementRecordStatus::RETURNED
            || $accrual->record_status_id === ManagementRecordStatus::REGISTERED
            || $accrual->record_status_id === ManagementRecordStatus::CANCELED)
            && $adjustedRequest['record_status_id'] === ManagementRecordStatus::APPROVED;

        if ($recordStatusChange) {
            // Obtener el monto del devengado del array ajustado
            $accrualAmount = $accrual->accrual_amount;
            // Compromiso ligado
            $commitment = $accrual->commitment;

            if ($recordStatusChange && $conditionsSubtractBalance) {
                // Crea un pago asociado
                $this->createPayment($accrual);
                // Actualiza el saldo del compromiso  
                return $this->updateCommitmentBalance($commitment, -$accrualAmount);
            }

            if ($recordStatusChange && $conditionsRevertBalance) {
                // Borra el pago asociado
                $accrual->payment->delete();
                // Actualiza el saldo del compromiso  
                return $this->updateCommitmentBalance($commitment, $accrualAmount);
            }
        }
    }

    private function updateCommitmentBalance(Commitment $commitment, $amount)
    {
        $commitment->balance += $amount;
        $commitment->save();
    }

    private function getAccrualBalance($request)
    {
        if ($request['treasury_approved'] === "approved") {
            return $request['accrual_amount'];
        }
        return null;
    }

    private function createPayment(Accrual $accrual)
    {
        $payment = new Payment([
            'current_management' => LaborManagements::STEP_1,
            'manager_status' => ManagementRecordStatus::NOT_REVIEWED,
        ]);

        $accrual->payment()->save($payment);
    }

    public function adjustParams($request)
    {
        $adjustedRequest = $request->validated();

        $currentManagement = $request['current_management'] ?? null;

        // Agregar fecha dependiendo del estado actual del devengado
        $dates = ['sec_cgf_date', 'assignment_date', 'accrual_date', 'coord_cgf_date'];
        $adjustedRequest[$dates[$currentManagement - 1]] = now();

        // Asignar la gestión siguiente dependiendo de la actual
        $adjustedRequest['current_management'] = $this->manageAssignment($request);

        // Asignar saldo si el monto devengado no es nulo y el saldo no está definido, y está aprobado. ó cero si no lo está
        $adjustedRequest['balance'] = $this->getAccrualBalance($request);

        // Manejar el estado del devengado
        $adjustedRequest['record_status_id'] = $this->manageRecordStatus($request);

        return $adjustedRequest;
    }

    protected function manageAssignment($adjustedRequest)
    {
        $currentManagement = $adjustedRequest['current_management'];
        $accrualMemo  = $adjustedRequest['accrual_memo'];
        $customerId  = $adjustedRequest['customer_id'];
        $recordStatusId  = $adjustedRequest['record_status_id'];
        $treasuryApproved  = $adjustedRequest['treasury_approved'];

        if (
            !is_null($accrualMemo) && is_null($customerId)
            && is_null($recordStatusId) && is_null($treasuryApproved)
        )
            return ManagementRoles::SEC_JAPC;

        if (
            !is_null($accrualMemo) && !is_null($customerId)
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
            !is_null($accrualMemo) && !is_null($customerId)
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
                || is_null($recordStatusId)) && $treasuryApproved === "returned")
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
                || is_null($recordStatusId)) && $treasuryApproved === "approved")
            || ($recordStatusId === ManagementRecordStatus::RETURNED
                && $treasuryApproved === "approved"
                && $currentManagement === ManagementRoles::ANALYST)
        ) {
            return ManagementRecordStatus::APPROVED;
        }

        if (
            // 1
            (($recordStatusId >= ManagementRecordStatus::REGISTERED
                || is_null($recordStatusId)) && $treasuryApproved === "canceled")
            || ($recordStatusId === ManagementRecordStatus::RETURNED
                && $treasuryApproved === "canceled"
                && $currentManagement === ManagementRoles::ANALYST)
        ) {
            return ManagementRecordStatus::CANCELED;
        }

        if (
            // 1
            (($recordStatusId >= ManagementRecordStatus::REGISTERED
                || is_null($recordStatusId)) && $treasuryApproved === "liquidated")
            || ($recordStatusId === ManagementRecordStatus::RETURNED
                && $treasuryApproved === "liquidated"
                && $currentManagement === ManagementRoles::ANALYST)
        ) {
            return ManagementRecordStatus::LIQUIDATED;
        }

        return $recordStatusId;
    }

    function getRole()
    {
        return auth()->user()->roles->first()->step;
    }
}

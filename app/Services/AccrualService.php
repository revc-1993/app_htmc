<?php

namespace App\Services;

use App\Models\Accrual;
use App\Http\Requests\StoreAccrualRequest;

class AccrualService
{
    const RECORD_STATUS = [
        'RETURNED' => 4,
        'REGISTERED' => 5,
        'APPROVED' => 6,
        'CANCELED' => 7,
        'LIQUIDATED' => 8,
    ];

    const MANAGEMENTS = [
        'SEC_JAPC' => 2,
        'ANALYST' => 3,
        'COORD_CGF' => 4,
    ];

    public function getAllAccruals()
    {
        return Accrual::with([
            'commitment', 'user', 'recordStatus'
        ])->get();
    }

    public function getAccrualForEdit(int $id)
    {
        return Accrual::with([
            'commitment' => function ($query) {
                $query->select('id', 'commitment_cur', 'contract_administrator', 'balance');
            },
        ])
            ->where('id', $id)
            ->firstOrFail();
    }

    public function createAccrual(StoreAccrualRequest $request)
    {
        $adjustedRequest = $this->adjustParams($request);
        return Accrual::create($adjustedRequest);
    }

    public function updateAccrual(Accrual $accrual, array $adjustedRequest)
    {
        $recordStatusChange = $accrual->record_status_id !== $adjustedRequest['record_status_id'];

        $accrual->update($adjustedRequest);

        if ($adjustedRequest['record_status_id'] === self::RECORD_STATUS['APPROVED'] && $recordStatusChange) {
            // Obtener el monto del compromiso del array ajustado
            $accrualAmount = $accrual->accrual_amount;

            // Actualizar la certificación relacionada restando el monto del balance
            $commitment = $accrual->commitment;
            $commitment->balance -= $accrualAmount;

            return $commitment->save();
        }

        if ($accrual->record_status_id === self::RECORD_STATUS['CANCELED'] && $recordStatusChange) {
            // Obtener el monto del devengado del array ajustado
            $accrualAmount = $accrual->accrual_amount;

            // Actualizar el compromiso relacionado restando el monto del balance
            $commitment = $accrual->commitment;
            $commitment->balance += $accrualAmount;

            return $commitment->save();
        }
    }

    public function deleteAccrual(Accrual $accrual)
    {
        if ($accrual->record_status_id === self::RECORD_STATUS['APPROVED']) {
            // Obtener el monto del devengado del array ajustado
            $accrualAmount = $accrual->commitment_amount;

            // Actualizar el compromiso relacionado restando el monto del balance
            $commitment = $accrual->commitment;
            $commitment->balance += $accrualAmount;

            $commitment->save();
        }

        $commitment->delete();
    }

    public function adjustParams($request)
    {
        $adjustedRequest = $request->validated();

        $currentManagement = $request['current_management'] ?? null;

        // Agregar fecha dependiendo del estado actual de la certificación
        $dates = ['sec_cgf_date', 'assignment_date', 'accrual_date', 'coord_cgf_date'];
        $adjustedRequest[$dates[$currentManagement - 1]] = now();

        // Asignar saldo si el monto del compromiso no es nulo y el saldo no está definido
        if (!isset($adjustedRequest['balance']) && isset($adjustedRequest['accrual_amount'])) {
            $adjustedRequest['balance'] = $adjustedRequest['accrual_amount'];
        }

        // Asignar la gestión siguiente dependiendo de la actual
        $adjustedRequest['current_management'] = $this->manageAssignment($request);

        // Manejar el estado de la certificación
        $adjustedRequest['record_status_id'] = $this->manageRecordStatus($request);

        return $adjustedRequest;
    }

    protected function manageAssignment($adjustedRequest)
    {
        $currentManagement = $adjustedRequest['current_management'];
        $customerId  = $adjustedRequest['customer_id'];
        $recordStatusId  = $adjustedRequest['record_status_id'];
        $treasuryApproved  = $adjustedRequest['treasury_approved'];

        if (
            !is_null($customerId)
            && (
                // 1. Estado de certificación vacío y Check de Coord. vacío
                (is_null($recordStatusId) && is_null($treasuryApproved))
                // 2. Estados antes de registrados (en revisión, devuelto); y con estado del Coord. "Returned"
                || ($recordStatusId < self::RECORD_STATUS['REGISTERED']
                    && (is_null($treasuryApproved) || $treasuryApproved === "returned"
                    )
                )
                // 3. Estado aprobado y aprobación "Returned"
                || (
                    ($recordStatusId > self::RECORD_STATUS['REGISTERED'] || is_null($recordStatusId))
                    && $treasuryApproved === "returned"
                )
                // 4. Cuando estado es registrado, aprobación del Coord es "Returned" y gestión actual es Coord. 
                || ($recordStatusId === self::RECORD_STATUS['REGISTERED'] && $treasuryApproved === "returned" && $currentManagement === self::MANAGEMENTS['COORD_CGF'])
            )
        )
            return self::MANAGEMENTS['ANALYST'];

        if (
            !is_null($customerId)
            && (
                // 1. Estado es registrado, y approved es nulo (primera vez)
                ($recordStatusId === self::RECORD_STATUS['REGISTERED'] && is_null($treasuryApproved))
                // 2. Estado es aprobado o liquidado, y 
                || (
                    ($recordStatusId >= self::RECORD_STATUS['REGISTERED'] || is_null($recordStatusId))
                    && $treasuryApproved !== "returned"
                )
                // 3. Si estado es Registrado, gestión actual es 3 y aprobación coord previa es devuelto  
                || ($recordStatusId === self::RECORD_STATUS['REGISTERED'] && $treasuryApproved === "returned" && $currentManagement === self::MANAGEMENTS['ANALYST'])
                // 4. Si estado es Devuelto, gestión actual es 3 y aprobación coord previa es aprobado o liquidado  
                || ($recordStatusId === self::RECORD_STATUS['RETURNED'] && $treasuryApproved !== "returned" && $currentManagement === self::MANAGEMENTS['ANALYST'])
            )
        )
            return self::MANAGEMENTS['COORD_CGF'];
    }

    protected function manageRecordStatus($adjustedRequest)
    {
        $recordStatusId = $adjustedRequest['record_status_id'];
        $treasuryApproved = $adjustedRequest['treasury_approved'];
        $currentManagement = $adjustedRequest['current_management'];

        if (
            // 1. Cuando previamente está aprobado
            (($recordStatusId > self::RECORD_STATUS['REGISTERED'] || is_null($recordStatusId)) && $treasuryApproved === "returned")
            // 2. 
            || ($recordStatusId === self::RECORD_STATUS['REGISTERED'] && $treasuryApproved === "returned" && $currentManagement === self::MANAGEMENTS['COORD_CGF'])
        ) {
            return self::RECORD_STATUS['RETURNED'];
        }

        if (
            // 1
            (($recordStatusId >= self::RECORD_STATUS['REGISTERED'] || is_null($recordStatusId)) && $treasuryApproved === "approved")
            || ($recordStatusId === self::RECORD_STATUS['RETURNED'] && $treasuryApproved === "approved" && $currentManagement === self::MANAGEMENTS['ANALYST'])
        ) {
            return self::RECORD_STATUS['APPROVED'];
        }

        if (
            // 1
            (($recordStatusId >= self::RECORD_STATUS['REGISTERED'] || is_null($recordStatusId)) && $treasuryApproved === "canceled")
            || ($recordStatusId === self::RECORD_STATUS['RETURNED'] && $treasuryApproved === "canceled" && $currentManagement === self::MANAGEMENTS['ANALYST'])
        ) {
            return self::RECORD_STATUS['CANCELED'];
        }

        if (
            // 1
            (($recordStatusId >= self::RECORD_STATUS['REGISTERED'] || is_null($recordStatusId)) && $treasuryApproved === "liquidated")
            || ($recordStatusId === self::RECORD_STATUS['RETURNED'] && $treasuryApproved === "liquidated" && $currentManagement === self::MANAGEMENTS['ANALYST'])
        ) {
            return self::RECORD_STATUS['LIQUIDATED'];
        }

        return $recordStatusId;
    }

    function getRole()
    {
        return auth()->user()->roles()->first()->id;
    }
}

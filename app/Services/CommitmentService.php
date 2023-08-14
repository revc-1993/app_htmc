<?php

namespace App\Services;

use App\Http\Requests\StoreCommitmentRequest;
use App\Models\Commitment;

class CommitmentService
{
    const RECORD_STATUS = [
        'RETURNED' => 4,
        'REGISTERED' => 5,
        'APPROVED' => 6,
        'CANCELED' => 7,
        'LIQUIDATED' => 8,
    ];

    const MANAGEMENTS = [
        'SEC_CGF' => 1,
        'SEC_JAPC' => 2,
        'ANALYST' => 3,
        'COORD_CGF' => 4,
    ];

    public function getAllCommitments()
    {
        return Commitment::with([
            'certification.vendor', 'user', 'recordStatus'
        ])
            ->filtered()
            ->get();
    }

    public function getCommitmentForEdit(int $id)
    {
        return Commitment::with([
            'certification' => function ($query) {
                $query->select('id', 'certification_number', 'contract_object', 'vendor_id', 'balance');
            },
            'certification.vendor' => function ($query) {
                $query->select('id', 'nit', 'name');
            },
            'vendor' => function ($query) {
                $query->select('id', 'nit', 'name');
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
        $recordStatusChange = $commitment->record_status_id !== $adjustedRequest['record_status_id'];

        // dd($recordStatusChange);

        $commitment->update($adjustedRequest);

        if ($adjustedRequest['record_status_id'] === self::RECORD_STATUS['APPROVED'] && $recordStatusChange) {
            // Obtener el monto del compromiso del array ajustado
            $commitmentAmount = $commitment->commitment_amount;

            // Actualizar la certificación relacionada restando el monto del balance
            $certification = $commitment->certification;
            $certification->balance -= $commitmentAmount;

            return $certification->save();
        }

        if ($commitment->record_status_id === self::RECORD_STATUS['CANCELED'] && $recordStatusChange) {
            // Obtener el monto del compromiso del array ajustado
            $commitmentAmount = $commitment->commitment_amount;

            // Actualizar la certificación relacionada restando el monto del balance
            $certification = $commitment->certification;
            $certification->balance += $commitmentAmount;

            return $certification->save();
        }
    }

    public function deleteCommitment(Commitment $commitment)
    {
        if ($commitment->record_status_id === self::RECORD_STATUS['APPROVED']) {
            // Obtener el monto del compromiso del array ajustado
            $commitmentAmount = $commitment->commitment_amount;

            // Actualizar la certificación relacionada restando el monto del balance
            $certification = $commitment->certification;
            $certification->balance += $commitmentAmount;

            $certification->save();
        }

        $commitment->delete();
    }

    public function adjustParams($request)
    {
        $adjustedRequest = $request->validated();

        $currentManagement = $request['current_management'] ?? null;

        // Agregar fecha dependiendo del estado actual de la certificación
        $dates = ['sec_cgf_date', 'assignment_date', 'commitment_date', 'coord_cgf_date'];
        $adjustedRequest[$dates[$currentManagement - 1]] = now();

        // Asignar saldo si el monto del compromiso no es nulo y el saldo no está definido
        if (!isset($adjustedRequest['balance']) && isset($adjustedRequest['commitment_amount'])) {
            $adjustedRequest['balance'] = $adjustedRequest['commitment_amount'];
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
        $contractAdministrator  = $adjustedRequest['contract_administrator'];
        $customerId  = $adjustedRequest['customer_id'];
        $recordStatusId  = $adjustedRequest['record_status_id'];
        $treasuryApproved  = $adjustedRequest['treasury_approved'];

        if (
            !is_null($contractAdministrator) && is_null($customerId)
            && is_null($recordStatusId) && is_null($treasuryApproved)
        )
            return self::MANAGEMENTS['SEC_JAPC'];

        if (
            !is_null($contractAdministrator) && !is_null($customerId)
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
            !is_null($contractAdministrator) && !is_null($customerId)
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

    public function getCommitmentByNumber($commitmentNumber)
    {

        // dd($certificationNumber);
        return Commitment::approved($commitmentNumber)
            ->first(['id', 'commitment_cur', 'contract_administrator', 'balance']);
    }

    function getRole()
    {
        return auth()->user()->roles()->first()->id;
    }
}

<?php

namespace App\Services;

use App\Constants\LaborManagements;
use App\Constants\ManagementPaymentStatus;
use App\Constants\ManagementRecordStatus;
use App\Constants\ManagementRoles;
use App\Models\Payment;

class PaymentService
{
    public function getAllPayments()
    {
        return Payment::with([
            'accrual',
            'accrual.commitment',
            'user',
            'currentManagement',
            'managerStatus',
            'analystStatus',
        ])->get();
    }

    public function getPaymentForEdit(int $id)
    {
        return Payment::with([
            'accrual',
            'accrual.commitment'
        ])
            ->where('id', $id)
            ->firstOrFail();
    }

    public function updatePayment(Payment $payment, array $adjustedRequest)
    {
        $payment->update($adjustedRequest);
    }

    public function adjustParams($request)
    {
        $adjustedRequest = $request->validated();

        $currentManagement = $request['current_management'] ?? null;

        // Agregar fecha dependiendo del estado actual del pago
        $dates = ['manager_date', 'analyst_date'];
        $adjustedRequest[$dates[$currentManagement - 1]] = now();

        // Asignar la gestión siguiente dependiendo de la actual
        $adjustedRequest['current_management'] = $this->manageAssignment($request);

        // Manejar el estado del pago
        $adjustedRequest['manager_status'] = $this->manageRecordStatus($request);

        return $adjustedRequest;
    }

    protected function manageAssignment($adjustedRequest)
    {
        $customer = $adjustedRequest['customer_id'];
        $analystStatus = $adjustedRequest['analyst_status'];

        if (
            is_null($customer)
            || $analystStatus === ManagementPaymentStatus::RETURNED
            || $analystStatus === ManagementPaymentStatus::REVIEWED
        ) {
            return LaborManagements::STEP_1;
        }

        return LaborManagements::STEP_2;
    }

    protected function manageRecordStatus($adjustedRequest)
    {
        $customer = $adjustedRequest['customer_id'];
        $analystStatus = $adjustedRequest['analyst_status'];
        $treasuryApproved = $adjustedRequest['treasury_approved'];

        if (is_null($customer)) {
            return ManagementPaymentStatus::NOT_REVIEWED;
        }

        if (
            $treasuryApproved === "returned"
            || ($analystStatus === ManagementPaymentStatus::RETURNED
                && is_null($treasuryApproved))
        ) {
            return ManagementPaymentStatus::RETURNED;
        }

        if ($treasuryApproved === "paid") {
            return ManagementPaymentStatus::PAID;
        }

        if (is_null($treasuryApproved) && $analystStatus === "reviewed") {
            return ManagementPaymentStatus::REVIEWED;
        }

        return ManagementPaymentStatus::IN_REVIEW;
    }
}

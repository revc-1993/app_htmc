<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Payment;
use App\Constants\Modules;
use Illuminate\Http\Request;
use App\Models\PaymentStatus;
use App\Services\PaymentService;
use App\Models\CurrentManagement;
use App\Http\Requests\UpdatePaymentRequest;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $payments = $this->paymentService->getAllPayments();
        // dd($payments);
        $users = User::role('treasury_analyst_role')->get();
        $roles = CurrentManagement::allProfiles(Modules::TREASURY)->get();
        $recordStatuses = PaymentStatus::all(['id', 'status']);

        return Inertia::render('Payments/Index', compact(
            'payments',
            'users',
            'roles',
            'recordStatuses'
        ));
    }

    public function edit(int $id)
    {
        $payment = $this->paymentService->getPaymentForEdit($id);
        $users = User::role('treasury_analyst_role')->get();
        $roles = CurrentManagement::allProfiles(Modules::TREASURY)->get();
        $recordStatuses = PaymentStatus::getRecordStatus()->get(['id', 'status']);

        return Inertia::render('Payments/Edit', compact(
            'payment',
            'users',
            'roles',
            'recordStatuses'
        ));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $adjustedRequest = $this->paymentService->adjustParams($request);
        $this->paymentService->updatePayment($payment, $adjustedRequest);

        $message = [
            "response" => "Registro actualizado correctamente.",
            "operation" => 3,
        ];

        return to_route('payments.index')->with(compact('message'));
    }
}

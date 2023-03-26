<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Vendor;
use Faker\Core\Number;
use App\Models\BudgetLine;
use App\Models\Commitment;
use App\Models\Department;
use App\Models\ExpenseType;
use App\Models\ProcessType;
use App\Models\RecordStatus;
use Illuminate\Http\Request;
use App\Models\Certification;
use App\Http\Requests\VendorRequest;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StoreCertificationRequest;
use App\Http\Requests\UpdateCertificationRequest;

class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Certifications/Index', [
            'certifications' => Certification
                ::with([
                    'user' => function ($query) {
                        $query->select('id', 'username');
                    },
                    'department' => function ($query) {
                        $query->select('id', 'department');
                    },
                    'processType' => function ($query) {
                        $query->select('id', 'process_type');
                    },
                    'expenseType' => function ($query) {
                        $query->select('id', 'expense_type');
                    },
                    'budgetLine' => function ($query) {
                        $query->select('id', 'budget_line');
                    },
                    'recordStatus' => function ($query) {
                        $query->select('id', 'status');
                    },
                    'vendor' => function ($query) {
                        $query->select('id', 'nit', 'name');
                    },
                ])
                ->filtered()
                ->get(),    // Aquí se puede especificar los campos especificos a extraer
            'departments' => Department::all(['id', 'department']),
            'processTypes' => ProcessType::all(['id', 'process_type']),
            'expenseTypes' => ExpenseType::all(['id', 'expense_type']),
            'budgetLines' => BudgetLine::all(['id', 'budget_line']),
            'users' => User::all(['id', 'name']),
            'recordStatuses' => RecordStatus::all(['id', 'status']),
        ]);
    }

    public function show(Certification $certification)
    {
        // return json_encode(['data'])
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Certifications/Create', [
            'departments' => Department::all(['id', 'department']),
            'processTypes' => ProcessType::all(['id', 'process_type']),
            'expenseTypes' => ExpenseType::all(['id', 'expense_type']),
            'budgetLines' => BudgetLine::all(['id', 'budget_line']),
            'users' => User::analystRole()->get(),
            'recordStatuses' => RecordStatus::getRecordStatus()->get(['id', 'status']),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCertificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCertificationRequest $request)
    {
        $adjustedRequest = $this->paramsControl($request);

        $certification = Certification::create($adjustedRequest);

        $message = [
            "response" => "Registro creado correctamente.",
            "operation" => 1,
        ];

        if ($certification->certification_memo === null)
            $message += ["comments" => "Atención: No se especificó un Nro. de Memorando de certificación."];

        return to_route('certifications.index')->with(compact('message'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Certification $certification
     * @return \Illuminate\Http\Response
     */
    public function edit($certification_id)
    {
        $certification = Certification::with([
            'vendor' => function ($query) {
                $query->select('id', 'nit', 'name');
            },
        ])
            ->where('certifications.id', '=', $certification_id)
            ->first();

        // dd($certification);

        return Inertia::render('Certifications/Edit', [
            'certification' => $certification,
            'departments' => Department::all(['id', 'department']),
            'processTypes' => ProcessType::all(['id', 'process_type']),
            'expenseTypes' => ExpenseType::all(['id', 'expense_type']),
            'budgetLines' => BudgetLine::all(['id', 'budget_line']),
            'users' => User::analystRole()->get(),
            'recordStatuses' => RecordStatus::getRecordStatus()->get(['id', 'status']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCertificationRequest  $request
     * @param  \App\Models\Certification  $certification
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCertificationRequest $request, Certification $certification)
    {
        $role = $this->getRole();

        $adjustedRequest = $this->paramsControl($request);

        $certification->update($adjustedRequest);

        $message = [
            "response" => "Registro actualizado correctamente.",
            "operation" => 3,
        ];

        if ($certification->certification_memo === null && $role <= 2)
            $message += ["comments" => "Atención: No se especificó un Nro. de Memorando de certificación."];

        return to_route('certifications.index')->with(compact('message'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Certification  $certification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Certification $certification)
    {
        $certification->delete();
        $message = [
            "response" => "Registro eliminado correctamente.",
            "operation" => 4,
        ];
        return to_route('certifications.index')->with(compact('message'));
    }

    protected function getRole()
    {
        return auth()->user()->roles()->first()->id;
    }

    protected function paramsControl($request)
    {
        $adjustedRequest = $request->validated() +
            $this->manageDate($request->current_management);

        $adjustedRequest['current_management'] = $this->manageAssignment($request);
        $adjustedRequest['record_status_id'] = $this->manageRecordStatus($request);

        return $adjustedRequest;
    }

    protected function manageDate(int $current_management)
    {
        if ($current_management === 1)        return  ['sec_cgf_date' => now()];
        else if ($current_management === 2)   return  ['assignment_date' => now()];
        else if ($current_management === 3)   return  ['cp_date' => now()];
        else if ($current_management === 4)   return  ['coord_cgf_date' => now()];
        else return [];
    }

    protected function manageAssignment($request)
    {

        if (
            !is_null($request->contract_object) && is_null($request->customer_id)
            && is_null($request->record_status_id) && is_null($request->treasury_approved)
        )
            return 2;

        if (
            !is_null($request->contract_object) && !is_null($request->customer_id)
            && (
                // 1
                (is_null($request->record_status_id) && is_null($request->treasury_approved))
                // 2
                || ($request->record_status_id < 4
                    && (is_null($request->treasury_approved) || $request->treasury_approved === "returned"
                    )
                )
                // 3
                || (
                    ($request->record_status_id > 4 || is_null($request->record_status_id))
                    && $request->treasury_approved === "returned"
                )
                // 4
                || ($request->record_status_id === 4 && $request->treasury_approved === "returned" && $request->current_management === 4)
            )
        )
            return 3;

        if (
            !is_null($request->contract_object) && !is_null($request->customer_id)
            && (
                // 1
                ($request->record_status_id === 4 && is_null($request->treasury_approved))
                // 2
                || (
                    ($request->record_status_id >= 4 || is_null($request->record_status_id))
                    && ($request->treasury_approved === "approved" || $request->treasury_approved === "liquidated")
                )
                // 3
                || ($request->record_status_id === 4 && $request->treasury_approved === "returned" && $request->current_management === 3)
            )
        )
            return 4;
    }

    protected function manageRecordStatus($request)
    {
        if (
            // 1
            (($request->record_status_id > 4 || is_null($request->record_status_id)) && $request->treasury_approved === "returned")
            // 2
            || ($request->record_status_id === 4 && $request->treasury_approved === "returned" && $request->current_management === 4)
        ) {
            return 5;
        }

        if (
            // 1
            (($request->record_status_id >= 4 || is_null($request->record_status_id)) && $request->treasury_approved === "approved")
        ) {
            return 6;
        }

        if (
            // 1
            (($request->record_status_id >= 4 || is_null($request->record_status_id)) && $request->treasury_approved === "liquidated")
        ) {
            return 7;
        }

        return $request->record_status_id;
    }

    public function getCertificationByNumber(Request $request)
    {
        $certification = Certification::approved($request->get('certification_number'))
            ->first(['id', 'certification_number', 'contract_object']);

        return response()->json(compact('certification'), 200);
    }
}

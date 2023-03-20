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
            'users' => User::analystCertification()->get(),
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
        $paramsControl = [
            'current_management' => $this->getRole() + 1,
            'sec_cgf_date' => now(),
        ];

        $certification = Certification::create($request->validated() + $paramsControl);

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
    public function edit(Certification $certification)
    {
        // dd($certification);
        return Inertia::render('Certifications/Edit', [
            'certification' => $certification,
            'departments' => Department::all(['id', 'department']),
            'processTypes' => ProcessType::all(['id', 'process_type']),
            'expenseTypes' => ExpenseType::all(['id', 'expense_type']),
            'budgetLines' => BudgetLine::all(['id', 'budget_line']),
            'users' => User::analystCertification()->get(),
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

        $paramsControl = $this->paramsControl($request, $role);
        $adjustedRequest = $request->validated();
        $adjustedRequest['record_status_id'] =
            $this->manageRecordStatus($request, $role);

        $certification->update($adjustedRequest + $paramsControl);

        $message = [
            "response" => "Registro actualizado correctamente.",
            "operation" => 3,
        ];
        if ($certification->certification_memo === null && $certification->current_management <= 2)
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

    protected function paramsControl(UpdateCertificationRequest $request, int $role)
    {
        return
            $this->manageDate($role) +
            $this->manageAssignment($role, $request->record_status_id, $request->treasury_approved);
    }

    protected function manageDate(int $role)
    {
        if ($role === 1)        return  ['sec_cgf_date' => now()];
        else if ($role === 2)   return  ['assignment_date' => now()];
        else if ($role === 3)   return  ['cp_date' => now()];
        else if ($role === 4)   return  ['coord_cgf_date' => now()];
        else return                     [];
    }

    protected function manageAssignment(int $role, $record_status_id, $treasury_approved)
    {
        if ($role < 3) {
            return ['current_management' => $role + 1];
        } else if ($role === 3) {
            if ($record_status_id < 4)
                return ['current_management' => 3];
            else
                return ['current_management' => $role + 1];
        } else if ($role === 4) {
            if ($treasury_approved === "returned")
                return ['current_management' => $role - 1];
            else
                return ['current_management' => $role];
        } else {
            return [];
        }
    }

    protected function manageRecordStatus(UpdateCertificationRequest $request, int $role)
    {
        if ($role === 4) {
            if ($request->treasury_approved === "returned")
                return 5;
            else if ($request->treasury_approved === "approved")
                return 6;
            else if ($request->treasury_approved === "liquidated")
                return 7;
            else
                return $request->record_status_id;
        } else {
            return $request->record_status_id;
        }
    }

    public function getVendorByNit(Request $request)
    {
        $vendor = Vendor::where('nit', $request->get('nit'))
            ->first(['id', 'nit', 'name']);
        return response()->json(compact('vendor'), 200);
    }

    public function getVendorById(Request $request)
    {
        $vendor = Vendor::where('id', $request->get('id'))
            ->first(['id', 'nit', 'name']);
        return response()->json(compact('vendor'), 200);
    }

    public function setVendor(VendorRequest $request)
    {
        $vendor = Vendor::create($request->validated());

        $message = [
            "response" => "Registro creado correctamente.",
            "operation" => 1,
        ];

        // dd(redirect()->getUrlGenerator()->previous());

        // return Redirect::back()

        return redirect()->back();
        //  'vendor_id', $vendor->id);;

        // return redirect(redirect()->getUrlGenerator()->previous())->with('vendor', $vendor);


        // return Inertia::render(
        //     'Certifications/Edit',
        //     [
        //         'vendor' => Inertia::lazy(fn () => $vendor),
        //     ]
        // );
        // redirect()->back();
    }
}

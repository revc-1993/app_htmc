<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\BudgetLine;
use App\Models\Department;
use App\Models\ExpenseType;
use App\Models\ProcessType;
use App\Models\RecordStatus;
use Illuminate\Http\Request;
use App\Services\CertificationService;
use App\Models\Certification;
use App\Http\Requests\StoreCertificationRequest;
use App\Http\Requests\UpdateCertificationRequest;
use App\Models\CustomRole;

class CertificationController extends Controller
{
    protected $certificationService;

    public function __construct(CertificationService $certificationService)
    {
        $this->middleware(
            'permission:create_certification|show_certification|update_certification|delete_certification',
            ['only' => ['index']]
        );

        $this->middleware(
            'permission:create_certification',
            ['only' => ['create', 'store']]
        );

        $this->middleware(
            'permission:update_certification',
            ['only' => ['edit', 'update']]
        );

        $this->middleware(
            'permission:delete_certification',
            ['only' => ['destroy']]
        );

        $this->certificationService = $certificationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $certifications = $this->certificationService->getAllCertifications();
        $departments = Department::all(['id', 'department']);
        $processTypes = ProcessType::all(['id', 'process_type']);
        $expenseTypes = ExpenseType::all(['id', 'expense_type']);
        $budgetLines = BudgetLine::all(['id', 'budget_line']);
        $users = User::all(['id', 'name']);
        $roles = CustomRole::allRoles();
        $recordStatuses = RecordStatus::all(['id', 'status']);

        return Inertia::render('Certifications/Index', compact(
            'certifications',
            'departments',
            'processTypes',
            'expenseTypes',
            'budgetLines',
            'users',
            'roles',
            'recordStatuses'
        ));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::all(['id', 'department']);
        $processTypes = ProcessType::all(['id', 'process_type']);
        $expenseTypes = ExpenseType::all(['id', 'expense_type']);
        $budgetLines = BudgetLine::all(['id', 'budget_line']);
        $users = User::analystRole()->get();
        $roles = CustomRole::allRoles();

        return Inertia::render('Certifications/Create', compact(
            'departments',
            'processTypes',
            'expenseTypes',
            'budgetLines',
            'users',
            'roles'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCertificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCertificationRequest $request)
    {
        $certification = $this->certificationService->createCertification($request);

        $message = [
            "response" => "Registro creado correctamente.",
            "operation" => 1,
        ];

        if (is_null($certification->certification_memo))
            $message["comments"] = "Atención: No se especificó un Nro. de Memorando de certificación.";

        return to_route('certifications.index')->with(compact('message'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Certification $certification
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $certification = $this->certificationService->getCertificationForEdit($id);
        $departments = Department::all(['id', 'department']);
        $processTypes = ProcessType::all(['id', 'process_type']);
        $expenseTypes = ExpenseType::all(['id', 'expense_type']);
        $budgetLines = BudgetLine::all(['id', 'budget_line']);
        $recordStatuses = RecordStatus::all(['id', 'status']);
        $users = User::analystRole()->get();
        $roles = CustomRole::allRoles();

        return Inertia::render('Certifications/Edit', compact(
            'certification',
            'departments',
            'processTypes',
            'expenseTypes',
            'budgetLines',
            'recordStatuses',
            'users',
            'roles'
        ));
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
        $role = $this->certificationService->getRole();
        $adjustedRequest = $this->certificationService->adjustParams($request);
        $this->certificationService->updateCertification($certification, $adjustedRequest);

        $message = [
            "response" => "Registro actualizado correctamente.",
            "operation" => 3,
        ];

        if (is_null($certification->certification_memo) && $role <= 2) {
            $message["comments"] = "Atención: No se especificó un Nro. de Memorando de certificación.";
        }

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

    public function getCertificationByNumber(Request $request)
    {
        $certification = $this->certificationService->getCertificationByNumber($request->get('certification_number'));
        return response()->json(compact('certification'), 200);
    }
}

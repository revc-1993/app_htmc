<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Certification;
use App\Http\Requests\StoreCertificationRequest;
use App\Http\Requests\UpdateCertificationRequest;
use App\Models\BudgetLine;
use App\Models\Commitment;
use App\Models\Department;
use App\Models\ProcessType;
use App\Models\ExpenseType;
use App\Models\RecordStatus;
use App\Models\User;
use Faker\Core\Number;

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
                ])
                ->filtered()
                ->get(),    // Aquí se puede especificar los campos especificos a extraer
            'departments' => Department::all(['id', 'department']),
            'process_types' => ProcessType::all(['id', 'process_type']),
            'expense_types' => ExpenseType::all(['id', 'expense_type']),
            'budget_lines' => BudgetLine::all(['id', 'budget_line']),
            'users' => User::analystCertification()->get(),
            'record_statuses' => RecordStatus::getRecordStatus()->get(['id', 'status']),
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

        $message = ["response" => "Registro creado correctamente."];

        if ($certification->certification_memo === null)
            $message += ["comments" => "Atención: No se especificó un Nro. de Memorando de certificación."];


        return to_route('certifications.index')->with(compact('message'));
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
        $adjustedRequest['record_status'] =
            $this->manageRecordStatus($request, $role);

        $certification->update($adjustedRequest + $paramsControl);

        $message = ["response" => "Registro actualizado correctamente."];
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
        $message = "Registro eliminado correctamente.";
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
            $this->manageAssignment($role, $request->record_status, $request->treasury_approved);
    }

    protected function manageRecordStatus(UpdateCertificationRequest $request, int $role)
    {
        if ($role === 4) {
            if ($request->treasury_approved === "returned")
                return 4;
            else if ($request->treasury_approved === "approved")
                return 5;
            else if ($request->treasury_approved === "liquidated")
                return 6;
            else
                return $request->record_status;
        } else {
            return $request->record_status;
        }
    }

    protected function manageDate(int $role)
    {
        if ($role === 1)        return  ['sec_cgf_date' => now()];
        else if ($role === 2)   return  ['assignment_date' => now()];
        else if ($role === 3)   return  ['cp_date' => now()];
        else if ($role === 4)   return  ['coord_cgf_date' => now()];
        else return                     [];
    }

    protected function manageAssignment(int $role, $record_status, $treasury_approved)
    {
        if ($role < 3 || $role === 3 && $record_status === 3) {
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
}

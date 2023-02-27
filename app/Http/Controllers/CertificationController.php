<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Certification;
use App\Http\Requests\StoreCertificationRequest;
use App\Http\Requests\UpdateCertificationRequest;
use App\Models\Commitment;
use App\Models\Department;
use App\Models\ProcessType;
use App\Models\ExpenseType;

class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd();
        return Inertia::render('Certifications/Index', [
            'certifications' => Certification
                ::with([
                    'user' => function ($query) {
                        $query->select('id', 'name');
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
                    'recordStatus' => function ($query) {
                        $query->select('id', 'department');
                    },
                ])
                ->pending()
                ->get(),
            'departments' => Department::all(['id', 'department']),
            'process_types' => ProcessType::all(['id', 'process_type']),
            'expense_types' => ExpenseType::all(['id', 'expense_type']),
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
        // dd($request);
        $certification = Certification::create($request->validated() + [
            'customer_id' => auth()->user()->id,
            'record_status' => auth()->user()->roles()->first()->id + 1,
            'cgf_date' => now(),
        ]);

        $message = "Registro creado correctamente.";
        return to_route('certifications.index')->with(compact('message'));


        // return to_route('certifications.index')
        //     ->with('message', 'Registro Nro. ' . $certification->id . " creado exitosamente. \n Estado: <strong>" . $certification->management_status . "</strong>");
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
        $certification->update($request->validated() + [
            'management_status' => $this->updateStatus($request),
        ]);

        $message = "Registro actualizado correctamente.";
        $state = $certification->management_status;

        if ($certification->management_status === 'Observado') {
            Commitment::create([
                'certification_id' => $certification->id,
                'customer_id' => $certification->customer_id,
            ]);
            $message .= "\nPuede revisar el nuevo compromiso en el módulo COMPROMISOS.";
        }

        return to_route('certifications.index')->with(compact('message', 'state'));
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

    // public function changeStatus(StoreCertificationRequest $request)
    // {
    //     if ($request->last_validation)
    //         return "Observado";
    //     else if (!is_null($request->certification_number) && !is_null($request->amount_to_commit))
    //         return "Certificado";
    //     else if (!is_null($request->budget_line) || !is_null($request->assignment_date) || !is_null($request->japc_reassignment_date) || !is_null($request->obligation_type) || !is_null($request->process_type))
    //         return "En revisión";
    //     else
    //         return "Pendiente de revisión";
    // }

    // public function updateStatus(UpdateCertificationRequest $request)
    // {
    //     if ($request->last_validation)
    //         return "Observado";
    //     else if (!is_null($request->certification_number) && !is_null($request->amount_to_commit))
    //         return "Certificado";
    //     else if (!is_null($request->budget_line) || !is_null($request->assignment_date) || !is_null($request->japc_reassignment_date) || !is_null($request->obligation_type) || !is_null($request->process_type))
    //         return "En revisión";
    //     else
    //         return "Pendiente de revisión";
    // }
}

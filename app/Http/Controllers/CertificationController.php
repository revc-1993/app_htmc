<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Certification;
use App\Http\Requests\StoreCertificationRequest;
use App\Http\Requests\UpdateCertificationRequest;
use App\Models\Commitment;
use App\Models\Department;

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
                        $query->select('id', 'name');
                    },
                    'department' => function ($query) {
                        $query->select('id', 'department');
                    }
                ])
                ->pending()
                ->orderBy("certifications.id", "desc")
                ->get(),
            'departments' => Department::all(['id', 'department']),
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
            'management_status' => $this->changeStatus($request),
        ]);

        $message = "Registro creado correctamente.";
        $state = $certification->management_status;
        return to_route('certifications.index')->with(compact('message', 'state'));


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
        return to_route('certifications.index');
    }

    public function changeStatus(StoreCertificationRequest $request)
    {
        if ($request->last_validation)
            return "Observado";
        else if (!is_null($request->certification_number) && !is_null($request->amount_to_commit))
            return "Certificado";
        else if (!is_null($request->budget_line) || !is_null($request->assignment_date) || !is_null($request->japc_reassignment_date) || !is_null($request->obligation_type) || !is_null($request->process_type))
            return "En revisión";
        else
            return "Pendiente de revisión";
    }

    public function updateStatus(UpdateCertificationRequest $request)
    {
        if ($request->last_validation)
            return "Observado";
        else if (!is_null($request->certification_number) && !is_null($request->amount_to_commit))
            return "Certificado";
        else if (!is_null($request->budget_line) || !is_null($request->assignment_date) || !is_null($request->japc_reassignment_date) || !is_null($request->obligation_type) || !is_null($request->process_type))
            return "En revisión";
        else
            return "Pendiente de revisión";
    }
}

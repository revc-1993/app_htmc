<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Certification;
use App\Http\Requests\StoreCertificationRequest;
use App\Http\Requests\UpdateCertificationRequest;
use App\Models\Department;

use function PHPUnit\Framework\isNull;

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
            'certifications' => Certification::query()
                // ->select('certifications.*', 'departments.*')
                // ->with('users:id', 'departments:department')
                ->select('certifications.*', 'users.name', 'departments.department')
                ->join('users', 'users.id', '=', 'certifications.customer_id')
                ->join('departments', 'departments.id', '=', 'certifications.department_id')
                ->pending()
                ->orderBy("certifications.id", "desc")->get(),
            'departments' => Department::all(),
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
        dd($certification);
        return to_route('certifications.index');
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
        return to_route('certifications.index');
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

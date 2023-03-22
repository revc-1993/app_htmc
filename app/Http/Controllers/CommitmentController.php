<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Commitment;
use App\Models\RecordStatus;
use Illuminate\Http\Request;
use App\Models\Certification;
use App\Http\Requests\StoreCommitmentRequest;
use App\Http\Requests\UpdateCommitmentRequest;

class CommitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Commitments/Index', [
            'commitments' => Commitment
                ::with([
                    'certification' => function ($query) {
                        $query->select('id', 'process_number');
                    },
                    'user' => function ($query) {
                        $query->select('id', 'username');
                    },
                    'vendor' => function ($query) {
                        $query->select('id', 'nit', 'name');
                    },
                    'recordStatus' => function ($query) {
                        $query->select('id', 'status');
                    },
                ])
                ->filtered()
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Commitments/Create', [
            'users' => User::analystRole()->get(),
            'recordStatuses' => RecordStatus::getRecordStatus()->get(['id', 'status']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommitmentRequest  $request
     * @param  \App\Models\Commitment  $commitment
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommitmentRequest $request, Commitment $commitment)
    {
        $paramsControl = [
            'current_management' => $this->getRole() + 1,
            'assignment_date' => now(),
        ];

        $commitment = Commitment::create($request->validated() + $paramsControl);

        $message = [
            "response" => "Registro creado correctamente.",
            "operation" => 1,
        ];

        if ($commitment->commitment_memo === null)
            $message += ["comments" => "Atención: No se especificó un Nro. de Memorando de compromiso."];

        return to_route('commitments.index')->with(compact('message'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Certification $certification
     * @return \Illuminate\Http\Response
     */
    public function edit(Commitment $commitment)
    {
        $commitment = $commitment
            ->with([
                'certification' => function ($query) {
                    $query->select('certification.id', 'certification.certification_number', 'certification.contract_object', 'certification.vendor_id');
                },
            ])
            ->with('certification.vendor')
            ->first();

        return Inertia::render('Commitments/Edit', [
            'commitment' => $commitment,
            'users' => User::analystRole()->get(),
            'recordStatuses' => RecordStatus::getRecordStatus()->get(['id', 'status']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCommitmentRequest  $request
     * @param  \App\Models\Commitment  $commitment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommitmentRequest $request, Commitment $commitment)
    {
        $role = $this->getRole();

        $paramsControl = $this->paramsControl($request, $role);
        $adjustedRequest = $request->validated();
        $adjustedRequest['record_status_id'] =
            $this->manageRecordStatus($request, $role);


        $commitment->update($adjustedRequest + $paramsControl);

        $message = [
            "response" => "Registro actualizado correctamente.",
            "operation" => 3,
        ];

        if ($commitment->commitment_memo === null && $role <= 2)
            $message += ["comments" => "Atención: No se especificó un Nro. de Memorando de compromiso."];

        return to_route('commitments.index')->with(compact('message'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Commitment  $commitment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Commitment $commitment)
    {
        $commitment->delete();
        $message = "Registro eliminado correctamente.";
        return to_route('commitments.index')->with(compact('message'));
    }

    protected function getRole()
    {
        return auth()->user()->roles()->first()->id;
    }

    protected function paramsControl(UpdateCommitmentRequest $request, int $role)
    {
        return
            $this->manageDate($role) +
            $this->manageAssignment($role, $request->record_status_id, $request->treasury_approved);
    }

    protected function manageDate(int $role)
    {
        if ($role === 2)   return  ['assignment_date' => now()];
        else if ($role === 3)   return  ['commitment_date' => now()];
        else if ($role === 4)   return  ['coord_cgf_date' => now()];
        else return                     [];
    }

    protected function manageAssignment(int $role, $record_status_id, $treasury_approved)
    {
        if ($role <= 2) {
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

    protected function manageRecordStatus(UpdateCommitmentRequest $request, int $role)
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
}

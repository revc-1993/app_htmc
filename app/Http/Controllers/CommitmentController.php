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
                        $query->select(
                            'certifications.id',
                            'certifications.certification_number',
                            'certifications.contract_object',
                            'certifications.vendor_id'
                        );
                    },
                    'certification.vendor' => function ($query) {
                        $query->select(
                            'vendors.id',
                            'vendors.name',
                        );
                    },
                    'user' => function ($query) {
                        $query->select('id', 'username');
                    },
                    'recordStatus' => function ($query) {
                        $query->select('id', 'status');
                    },
                ])
                ->filtered()
                ->get(),
            'users' => User::analystRole()->get(),
            'recordStatuses' => RecordStatus::getRecordStatus()->get(['id', 'status']),
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
        $adjustedRequest = $this->paramsControl($request);

        $commitment = Commitment::create($adjustedRequest);

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
    public function edit($commitment_id)
    {
        $commitment = Commitment::with([
            'certification' => function ($query) {
                $query->select(
                    'certifications.id',
                    'certifications.certification_number',
                    'certifications.contract_object',
                    'certifications.vendor_id',
                );
            },
            'certification.vendor' => function ($query) {
                $query->select(
                    'vendors.id',
                    'vendors.nit',
                    'vendors.name',
                );
            },
        ])
            ->where('commitments.id', '=', $commitment_id)
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
        // dd($request);

        $role = $this->getRole();

        $adjustedRequest = $this->paramsControl($request);

        $commitment->update($adjustedRequest);

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
        $message = [
            "response" => "Registro eliminado correctamente.",
            "operation" => 4,
        ];
        return to_route('commitments.index')->with(compact('message'));
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
        if ($current_management === 2)          return  ['assignment_date' => now()];
        else if ($current_management === 3)     return  ['commitment_date' => now()];
        else if ($current_management === 4)     return  ['coord_cgf_date' => now()];
        else return [];
    }

    protected function manageAssignment($request)
    {

        if (
            !is_null($request->certification_id)
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
        ) {
            return 3;
        }

        if (
            !is_null($request->certification_id)
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
        ) {
            return 4;
        }
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
}

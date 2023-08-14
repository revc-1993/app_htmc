<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Commitment;
use App\Models\CustomRole;
use App\Models\RecordStatus;
use App\Http\Requests\StoreCommitmentRequest;
use App\Http\Requests\UpdateCommitmentRequest;
use App\Services\CommitmentService;

class CommitmentController extends Controller
{
    protected $commitmentService;

    public function __construct(CommitmentService $commitmentService)
    {
        $this->middleware(
            'permission:create_commitment|show_commitment|update_commitment|delete_commitment',
            ['only' => ['index']]
        );

        $this->middleware(
            'permission:create_commitment',
            ['only' => ['create', 'store']]
        );

        $this->middleware(
            'permission:update_commitment',
            ['only' => ['edit', 'update']]
        );

        $this->middleware(
            'permission:delete_commitment',
            ['only' => ['destroy']]
        );

        $this->commitmentService = $commitmentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commitments = $this->commitmentService->getAllCommitments();
        $users = User::analystRole()->get();
        $roles = CustomRole::allRoles();
        $recordStatuses = RecordStatus::all(['id', 'status']);

        return Inertia::render('Commitments/Index', compact(
            'commitments',
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
        $users = User::analystRole()->get();
        $roles = CustomRole::allRoles();
        $recordStatuses = RecordStatus::getRecordStatus()->get(['id', 'status']);

        return Inertia::render('Commitments/Create', compact(
            'users',
            'roles',
            'recordStatuses'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommitmentRequest  $request
     * @param  \App\Models\Commitment  $commitment
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommitmentRequest $request)
    {
        $commitment = $this->commitmentService->createCommitment($request);

        $message = [
            "response" => "Registro creado correctamente.",
            "operation" => 1,
        ];

        if (is_null($commitment->commitment_memo))
            $message['comments'] = "Atención: No se especificó un Nro. de Memorando de compromiso.";

        return to_route('commitments.index')->with(compact('message'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Certification $certification
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $commitment = $this->commitmentService->getCommitmentForEdit($id);
        $users = User::analystRole()->get();
        $roles = CustomRole::allRoles();
        $recordStatuses = RecordStatus::getRecordStatus()->get(['id', 'status']);

        return Inertia::render('Commitments/Edit', compact(
            'commitment',
            'users',
            'roles',
            'recordStatuses'
        ));
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
        $role = $this->commitmentService->getRole();
        $adjustedRequest = $this->commitmentService->adjustParams($request);
        $this->commitmentService->updateCommitment($commitment, $adjustedRequest);

        $message = [
            "response" => "Registro actualizado correctamente.",
            "operation" => 3,
        ];

        if (is_null($commitment->commitment_memo) && $role <= 2)
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
        $this->commitmentService->deleteCommitment($commitment);

        $message = [
            "response" => "Registro eliminado correctamente.",
            "operation" => 4,
        ];
        return to_route('commitments.index')->with(compact('message'));
    }
}

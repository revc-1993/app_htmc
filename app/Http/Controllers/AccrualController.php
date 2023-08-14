<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Accrual;
use App\Models\CustomRole;
use App\Models\RecordStatus;
use App\Services\AccrualService;
use App\Http\Requests\StoreAccrualRequest;
use App\Http\Requests\UpdateAccrualRequest;

class AccrualController extends Controller
{
    protected $accrualService;

    public function __construct(AccrualService $accrualService)
    {
        $this->middleware(
            'permission:create_accrual|show_accrual|update_accrual|delete_accrual',
            ['only' => ['index']]
        );

        $this->middleware(
            'permission:create_accrual',
            ['only' => ['create', 'store']]
        );

        $this->middleware(
            'permission:update_accrual',
            ['only' => ['edit', 'update']]
        );

        $this->middleware(
            'permission:delete_accrual',
            ['only' => ['destroy']]
        );

        $this->accrualService = $accrualService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accruals = $this->accrualService->getAllAccruals();
        $users = User::analystRole()->get();
        $roles = CustomRole::allRoles();
        $recordStatuses = RecordStatus::all(['id', 'status']);

        return Inertia::render('Accruals/Index', compact(
            'accruals',
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

        return Inertia::render('Accruals/Create', compact(
            'users',
            'roles',
            'recordStatuses'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAccrualRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccrualRequest $request)
    {
        $accrual = $this->accrualService->createAccrual($request);

        $message = [
            "response" => "Registro creado correctamente.",
            "operation" => 1,
        ];

        if (is_null($accrual->accrual_memo))
            $message['comments'] = "Atención: No se especificó un Nro. de Memorando de devengado.";

        return to_route('accruals.index')->with(compact('message'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Accrual  $accrual
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $accrual = $this->accrualService->getAccrualForEdit($id);
        $users = User::analystRole()->get();
        $roles = CustomRole::allRoles();
        $recordStatuses = RecordStatus::getRecordStatus()->get(['id', 'status']);

        return Inertia::render('Accruals/Edit', compact(
            'accrual',
            'users',
            'roles',
            'recordStatuses'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAccrualRequest  $request
     * @param  \App\Models\Accrual  $accrual
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccrualRequest $request, Accrual $accrual)
    {
        $role = $this->accrualService->getRole();
        $adjustedRequest = $this->accrualService->adjustParams($request);
        $this->accrualService->updateAccrual($accrual, $adjustedRequest);

        $message = [
            "response" => "Registro actualizado correctamente.",
            "operation" => 3,
        ];

        if (is_null($accrual->accrual_memo) && $role <= 2)
            $message += ["comments" => "Atención: No se especificó un Nro. de Memorando de devengado."];

        return to_route('accruals.index')->with(compact('message'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Accrual  $accrual
     * @return \Illuminate\Http\Response
     */
    public function destroy(Accrual $accrual)
    {
        $this->accrualService->deleteAccrual($accrual);

        $message = [
            "response" => "Registro eliminado correctamente.",
            "operation" => 4,
        ];
        return to_route('accruals.index')->with(compact('message'));
    }
}

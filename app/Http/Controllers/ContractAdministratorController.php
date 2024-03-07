<?php

namespace App\Http\Controllers;

use App\Models\ContractAdministrator;
use Illuminate\Http\Request;
use App\Http\Requests\StoreContractAdministratorRequest;
use App\Http\Requests\UpdateContractAdministratorRequest;

class ContractAdministratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreContractAdministratorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContractAdministratorRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ContractAdministrator  $contractAdministrator
     * @return \Illuminate\Http\Response
     */
    public function show(ContractAdministrator $contractAdministrator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ContractAdministrator  $contractAdministrator
     * @return \Illuminate\Http\Response
     */
    public function edit(ContractAdministrator $contractAdministrator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateContractAdministratorRequest  $request
     * @param  \App\Models\ContractAdministrator  $contractAdministrator
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContractAdministratorRequest $request, ContractAdministrator $contractAdministrator)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ContractAdministrator  $contractAdministrator
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContractAdministrator $contractAdministrator)
    {
        //
    }

    public function getContractAdministratorByCi(Request $request)
    {
        $contractAdministrator = ContractAdministrator::whereCi($request->get('ci'))
            ->first(['id', 'ci', 'names']);
        return response()->json(compact('contractAdministrator'), 200);
    }
}

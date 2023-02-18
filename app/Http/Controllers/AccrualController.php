<?php

namespace App\Http\Controllers;

use App\Models\Accrual;
use App\Http\Requests\StoreAccrualRequest;
use App\Http\Requests\UpdateAccrualRequest;

class AccrualController extends Controller
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
     * @param  \App\Http\Requests\StoreAccrualRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccrualRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Accrual  $accrual
     * @return \Illuminate\Http\Response
     */
    public function show(Accrual $accrual)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Accrual  $accrual
     * @return \Illuminate\Http\Response
     */
    public function edit(Accrual $accrual)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Accrual  $accrual
     * @return \Illuminate\Http\Response
     */
    public function destroy(Accrual $accrual)
    {
        //
    }
}

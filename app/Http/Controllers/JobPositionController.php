<?php

namespace App\Http\Controllers;

use App\Models\JobPosition;
use App\Http\Requests\StoreJobPositionRequest;
use App\Http\Requests\UpdateJobPositionRequest;

class JobPositionController extends Controller
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
     * @param  \App\Http\Requests\StoreJobPositionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJobPositionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JobPosition  $jobPosition
     * @return \Illuminate\Http\Response
     */
    public function show(JobPosition $jobPosition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JobPosition  $jobPosition
     * @return \Illuminate\Http\Response
     */
    public function edit(JobPosition $jobPosition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateJobPositionRequest  $request
     * @param  \App\Models\JobPosition  $jobPosition
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJobPositionRequest $request, JobPosition $jobPosition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JobPosition  $jobPosition
     * @return \Illuminate\Http\Response
     */
    public function destroy(JobPosition $jobPosition)
    {
        //
    }
}

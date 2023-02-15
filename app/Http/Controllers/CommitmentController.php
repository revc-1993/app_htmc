<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Commitment;
use App\Http\Requests\StoreCommitmentRequest;
use App\Http\Requests\UpdateCommitmentRequest;
use App\Models\Certification;

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
                        $query->select('id', 'certification_number');
                    },
                ])
                ->orderBy("commitments.id", "desc")
                ->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommitmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Certification $certification)
    {
        Commitment::create([
            'certification_id' => $certification->id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Commitment  $commitment
     * @return \Illuminate\Http\Response
     */
    public function show(Commitment $commitment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Commitment  $commitment
     * @return \Illuminate\Http\Response
     */
    public function edit(Commitment $commitment)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Commitment  $commitment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Commitment $commitment)
    {
        //
    }
}

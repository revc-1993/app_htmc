<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Commitment;
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
                        $query->select('id', 'certification_number');
                    },
                ])
                ->orderBy("commitments.id", "desc")
                ->get(),
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
        $commitment->update($request->validated());

        $message = "Registro actualizado correctamente.";
        $state = $commitment->management_status;

        // if ($commitment->management_status === 'Observado') {
        //     Commitment::create([
        //         'certification_id' => $commitment->id,
        //     ]);
        //     $message .= "\nPuede revisar el nuevo compromiso en el módulo COMPROMISOS.";
        // }

        return to_route('commitments.index')->with(compact('message', 'state'));
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
        return to_route('commitments.index');
    }
}

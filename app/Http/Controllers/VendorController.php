<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Requests\VendorRequest;
use Illuminate\Support\Facades\Redirect;

class VendorController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\VendorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VendorRequest $request)
    {
        $vendor = Vendor::create($request->validated());

        $message = [
            "response" => "Registro creado correctamente.",
            "operation" => 1,
        ];

        // return response()->json(compact('message', 'vendor'));
        return inertia('Certifications/Edit', compact('message', 'vendor'));
    }
}

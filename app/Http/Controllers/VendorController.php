<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function getVendor(Request $request)
    {
        dd($request);

        $vendor = Vendor::where('nit', $request->search)->first();

        return response()->json($vendor, 200);
    }
}

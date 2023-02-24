<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Commitment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChartController extends Controller
{
    public function dashboard()
    {
        $ncertifications = Certification::count();
        $ncertificationsOk = Certification::where('certification_status', '=', 'Observado')->count();
        $certifications_percent = $ncertifications > 0 ? ceil($ncertificationsOk / $ncertifications * 100) : 0;
        // $ncommitments = Commitment::count();
        // $ncommitmentsOk = Commitment::where('certification_status', '=', 'Observado')->count();
        // $commitments_percent = $ncommitments > 0 ? ceil($ncommitmentsOk / $ncommitments * 100) : 0;

        $pending_certifications = Certification
            ::with([
                'user' => function ($query) {
                    $query->select('id', 'name');
                },
                'department' => function ($query) {
                    $query->select('id', 'department');
                }
            ])
            ->notReviewed()
            ->take(5)
            ->get();

        $certifications_amount_ordered = Certification
            ::with([
                'user' => function ($query) {
                    $query->select('id', 'name');
                },
                'department' => function ($query) {
                    $query->select('id', 'department');
                }
            ])
            ->amountOrdered()
            ->take(5)
            ->get();


        return Inertia::render('HomeView', [
            'n_certifications_ok' => $ncertificationsOk,
            'certifications_percent' => $certifications_percent,
            // 'n_commitments_ok' => $ncommitmentsOk,
            // 'commitments_percent' => $commitments_percent,
            'pending_certifications' => $pending_certifications,
            'certifications_amount_ordered' => $certifications_amount_ordered,
        ]);
    }
}

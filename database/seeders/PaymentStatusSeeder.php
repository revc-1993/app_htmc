<?php

namespace Database\Seeders;

use App\Models\PaymentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $statuses = [
            "PENDIENTE DE REVISIÓN",
            "EN REVISIÓN",
            "REVISADO",
            "DEVUELTO",
            "PAGADO",
        ];
        foreach ($statuses as $status) {
            PaymentStatus::create(['status' => $status]);
        }
    }
}

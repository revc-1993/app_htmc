<?php

namespace Database\Seeders;

use App\Models\RecordStatus;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RecordStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // *---- ESTADOS ----*
        $statuses = [
            "PENDIENTE DE REVISIÓN",
            "EN REVISIÓN",
            "OBSERVADO",
            "DEVUELTO",
            "REGISTRADO",
            "APROBADO",
            "ANULADO",
            "LIQUIDADO",
        ];
        foreach ($statuses as $status) {
            RecordStatus::create(['status' => $status]);
        }
    }
}

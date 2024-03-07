<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurrentManagement;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrentManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // *---- GESTION ACTUAL ----*
        CurrentManagement::query()->delete();

        $csvPath = storage_path('app/csv/current_management.csv');

        if (File::exists($csvPath)) {
            $csvData = array_map(
                'str_getcsv',
                file($csvPath)
            );
            array_shift($csvData);

            foreach ($csvData as $row) {
                CurrentManagement::create([
                    'name' => $row[0],
                    'module' => $row[1],
                    'treasury_name' => $row[2],
                    'step' => $row[3],
                ]);
            }
        }
    }
}

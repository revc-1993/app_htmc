<?php

namespace Database\Seeders;

use App\Models\ProcessType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\File;

class ProcessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProcessType::query()->delete();

        $csvPath = storage_path('app/csv/process_type.csv');

        if (File::exists($csvPath)) {
            $csvData = array_map('str_getcsv', file($csvPath));
            array_shift($csvData);

            foreach ($csvData as $row) {
                ProcessType::create([
                    'process_type' => $row[0],
                ]);
            }
        }
    }
}

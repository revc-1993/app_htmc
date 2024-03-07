<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::query()->delete();

        $csvPath = storage_path('app/csv/departments.csv');

        if (File::exists($csvPath)) {
            $csvData = array_map('str_getcsv', file($csvPath));
            array_shift($csvData);

            foreach ($csvData as $row) {
                Department::create([
                    'department' => $row[1],
                    'area' => $row[0],
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\BudgetLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BudgetLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BudgetLine::query()->delete();

        $csvPath = storage_path('app/csv/budget_lines.csv');

        if (File::exists($csvPath)) {
            $csvData = array_map('str_getcsv', file($csvPath));
            array_shift($csvData);

            foreach ($csvData as $row) {
                BudgetLine::create([
                    'budget_line' => $row[0],
                    'description' => $row[1],
                ]);
            }
        }
    }
}

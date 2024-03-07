<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractAdministrator;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContractAdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContractAdministrator::query()->delete();

        $csvPath = storage_path('app/csv/contract_administrators.csv');

        if (File::exists($csvPath)) {
            $csvData = array_map('str_getcsv', file($csvPath));
            array_shift($csvData);

            foreach ($csvData as $row) {
                ContractAdministrator::create([
                    'ci' => $row[0],
                    'name' => $row[1],
                    'last_name' => $row[2],
                    'names' => $row[3],
                ]);
            }
        }
    }
}

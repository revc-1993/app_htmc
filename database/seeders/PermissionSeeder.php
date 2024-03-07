<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;
use App\Repositories\PermissionRepository;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // *---- PERMISOS ----*
        Permission::query()->delete();

        $csvPath = storage_path('app/csv/permissions.csv');

        if (File::exists($csvPath)) {
            $csvData = array_map('str_getcsv', file($csvPath));
            array_shift($csvData);

            foreach ($csvData as $row) {
                Permission::create([
                    'name' => $row[1],
                ]);
            }
        }
    }
}

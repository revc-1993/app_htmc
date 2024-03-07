<?php

namespace Database\Seeders;

use App\Models\CustomRole;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    private $permissions = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomRole::query()->delete();
        $csvPath = storage_path('app/csv/roles_and_permissions.csv');

        if (file_exists($csvPath)) {
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setHeaderOffset(0);

            foreach ($csv as $row) {
                $roleName = $row['role'];
                $nickname = $row['nickname'];
                $step = $row['step'];
                $permissionName = $row['permission'];

                $customRole = CustomRole::updateOrCreate([
                    'name' => $roleName,
                    'nickname' => $nickname,
                    'step' => $step,
                ]);

                $permissionModel = Permission::updateOrCreate(['name' => $permissionName]);

                $customRole->givePermissionTo($permissionModel);
            }
        }
    }
}

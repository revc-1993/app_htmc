<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use League\Csv\Reader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    private $roles = [];
    private $users = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->delete();
        $csvPath = storage_path('app/csv/users_and_roles.csv');

        if (file_exists($csvPath)) {
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setHeaderOffset(0);

            foreach ($csv as $row) {
                $name = $row['name'];
                $username = $row['username'];
                $email = $row['email'];
                $department = $row['department'];
                $password = $row['password'];
                $roleName = $row['role'];

                $user = User::create([
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'department' => $department,
                    'password' => bcrypt($password),
                ]);

                $role = CustomRole::findByName($roleName);
                $user->assignRole($role);
            }
        }
    }
}

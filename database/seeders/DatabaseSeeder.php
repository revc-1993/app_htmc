<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\JobPosition;
use App\Models\Certification;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $department = Department::factory(3)->create();
        // dd($department);
        // factory(JobPosition::class)->create(['department_id' => $department->id]);
        JobPosition::factory(5)->create();

        $user1 = User::factory()->create([
            'name' => 'Usuario 1',
            'email' => 'usuario1@gmail.com',
            'department' => 'Financiero',
            'job_position' => 'Analista',
            'password' => '$2y$10$i8bR6d58xUikIQiBu.MT..px1q70ZewUuz9PMembnh9dDCvR6ud7u',
            'admin_since' => now(),
        ]);
        Certification::factory(2)->create(['customer_id' => $user1->id]);
        $user2 = User::factory()->create([
            'name' => 'Usuario 2',
            'email' => 'usuario2@gmail.com',
            'department' => 'Despacho',
            'job_position' => 'Analista',
            'password' => '$2y$10$i8bR6d58xUikIQiBu.MT..px1q70ZewUuz9PMembnh9dDCvR6ud7u',
            'admin_since' => now(),
        ]);
        Certification::factory(2)->create(['customer_id' => $user2->id]);
    }
}

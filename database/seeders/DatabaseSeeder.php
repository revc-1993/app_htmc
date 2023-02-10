<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
        \App\Models\User::factory()->create([
            'name' => 'Ronny Vera',
            'email' => 'ronny.verac.1993@gmail.com',
            'department' => 'Financiero',
            'job_position' => 'Analista',
            'password' => '$2y$10$i8bR6d58xUikIQiBu.MT..px1q70ZewUuz9PMembnh9dDCvR6ud7u',
            'admin_since' => now(),
        ]);

        Certification::factory(20)->create();
        // \App\Models\User::factory(10)->create();

    }
}

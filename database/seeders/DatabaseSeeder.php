<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\JobPosition;
use App\Models\Certification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $role_secretario_cgf = Role::create(['name' => 'secretario_cgf']);
        $role_secretario_presupuesto = Role::create(['name' => 'secretario_presupuesto']);
        $permission_crear_certificacion = Permission::create(['name' => 'registro_certificacion']);
        $permission_editar_certificacion = Permission::create(['name' => 'edicion_certificacion']);

        $permissions_secretario_cgf = [$permission_crear_certificacion, $permission_editar_certificacion];
        $role_secretario_cgf->syncPermissions($permissions_secretario_cgf);

        $role_secretario_presupuesto->givePermissionTo($permission_editar_certificacion);


        $department = Department::factory(3)->create();

        $user1 = User::factory()->create([
            'name' => 'Secretario CGF 1',
            'email' => 'usuario1@gmail.com',
            'department' => 'Financiero',
            'password' => '$2y$10$i8bR6d58xUikIQiBu.MT..px1q70ZewUuz9PMembnh9dDCvR6ud7u',
            'admin_since' => now(),
        ]);
        $user1->assignRole($role_secretario_cgf);
        Certification::factory(2)->create(['customer_id' => $user1->id]);
        $user2 = User::factory()->create([
            'name' => 'Secretario Presupuesto 1',
            'email' => 'usuario2@gmail.com',
            'department' => 'Despacho',
            'job_position' => 'Analista',
            'password' => '$2y$10$i8bR6d58xUikIQiBu.MT..px1q70ZewUuz9PMembnh9dDCvR6ud7u',
            'admin_since' => now(),
        ]);
        $user2->assignRole($role_secretario_presupuesto);
        Certification::factory(2)->create(['customer_id' => $user2->id]);
    }
}

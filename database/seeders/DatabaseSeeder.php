<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\ProcessType;
use App\Models\ExpenseType;
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
        // *---- ROLES ----*
        // Rol 1: Secretario CGF: Crear certificación, actualizar certificación (STEP 1)
        $cgf_secretary_role = Role::create(['name' => 'cgf_secretary_role']);
        // Rol 2: Secretario JAPC: actualizar certificación (STEP 2)
        $japc_secretary_role = Role::create(['name' => 'japc_secretary_role']);
        // Rol 3: Analita de certificación: actualizar certificación (STEP 3)
        $certification_analyst_role = Role::create(['name' => 'certification_analyst_role']);
        // Rol 4: Analita de tesorería: actualizar certificación (STEP 4)
        $treasury_analyst_role = Role::create(['name' => 'treasury_analyst_role']);
        // Rol 5: Responsable de financiero: ver certificación
        $cfo_role = Role::create(['name' => 'cfo_role']);  // Responsable Financiero
        // Rol 5: Administrador: todos los permisos (STEP 1,2,3,4)
        $admin_role = Role::create(['name' => 'admin_role']);  // Admin

        // *---- PERMISOS ----*
        // Permiso 1: Crear certificación
        $create_certification = Permission::create(['name' => 'create_certification']);
        // Permiso 2: Ver certificación
        $show_certification = Permission::create(['name' => 'show_certification']);
        // Permiso 3: Editar certificación
        $update_certification = Permission::create(['name' => 'update_certification']);
        // Permiso 4: Eliminar certificación
        $delete_certification = Permission::create(['name' => 'delete_certification']);
        // Permiso 5: Datos del Secretario CGF
        $cgf_certification = Permission::create(['name' => 'cgf_certification']);
        // Permiso 6: Datos del Secretario JAPC
        $japc_certification = Permission::create(['name' => 'japc_certification']);
        // Permiso 7: Datos del Analista de Certificación
        $financial_certification = Permission::create(['name' => 'financial_certification']);
        // Permiso 8: Datos del Analista de Tesorería
        $treasury_certification = Permission::create(['name' => 'treasury_certification']);

        // *---- ASIGNA PERMISOS A ROLES ----*
        $cgf_secretary_role->syncPermissions([
            $create_certification, $show_certification, $update_certification,
            $cgf_certification,
        ]);
        $japc_secretary_role->syncPermissions([
            $show_certification, $update_certification,
            $cgf_certification, $japc_certification,
        ]);
        $certification_analyst_role->syncPermissions([
            $show_certification, $update_certification,
            $cgf_certification, $japc_certification, $financial_certification,
        ]);
        $treasury_analyst_role->syncPermissions([
            $show_certification, $update_certification,
            $cgf_certification, $japc_certification,
            $financial_certification, $treasury_certification,

        ]);
        $cfo_role->syncPermissions([
            $show_certification,
            $cgf_certification, $japc_certification,
            $financial_certification, $treasury_certification,
        ]);
        $admin_role->syncPermissions([
            $create_certification, $show_certification, $update_certification,
            $cgf_certification, $japc_certification, $financial_certification, $treasury_certification,
        ]);

        // *---- DEPARTAMENTOS ----*
        $department = Department::factory(3)->create();

        // *---- TIPOS DE PROCESO ----*
        $process_type = ProcessType::factory(3)->create();

        // *---- TIPOS DE GASTO ----*
        $expense_type = ExpenseType::factory(3)->create();

        // *---- USUARIOS ----*
        $user_cgf_1 = User::factory()->create([
            'name' => 'Secretario CGF 1',
            'email' => 'secretario_cgf_1@gmail.com',
            'department' => 'HTMC',
            'password' => '$2y$10$i8bR6d58xUikIQiBu.MT..px1q70ZewUuz9PMembnh9dDCvR6ud7u',
        ]);
        $user_japc_1 = User::factory()->create([
            'name' => 'Secretario JAPC 1',
            'email' => 'secretario_japc_1@gmail.com',
            'department' => 'HTMC',
            'password' => '$2y$10$i8bR6d58xUikIQiBu.MT..px1q70ZewUuz9PMembnh9dDCvR6ud7u',
        ]);
        $user_cert_analyst_1 = User::factory()->create([
            'name' => 'Analista de Certificación 1',
            'email' => 'analista_certificacion_1@gmail.com',
            'department' => 'HTMC',
            'password' => '$2y$10$i8bR6d58xUikIQiBu.MT..px1q70ZewUuz9PMembnh9dDCvR6ud7u',
        ]);
        $user_treasury_1 = User::factory()->create([
            'name' => 'Analista de Tesorería 1',
            'email' => 'analista_tesoreria_1@gmail.com',
            'department' => 'HTMC',
            'password' => '$2y$10$i8bR6d58xUikIQiBu.MT..px1q70ZewUuz9PMembnh9dDCvR6ud7u',
        ]);
        $user_admin = User::factory()->create([
            'name' => 'Ronny Vera',
            'email' => 'root@gmail.com',
            'department' => 'HTMC',
            'password' => '$2y$10$i8bR6d58xUikIQiBu.MT..px1q70ZewUuz9PMembnh9dDCvR6ud7u',
        ]);

        // *---- ASIGNA ROLES A USUARIOS ----*
        $user_cgf_1->assignRole($cgf_secretary_role);
        $user_japc_1->assignRole($japc_secretary_role);
        $user_cert_analyst_1->assignRole($certification_analyst_role);
        $user_treasury_1->assignRole($treasury_analyst_role);
        $user_admin->assignRole($admin_role);

        Certification::factory(2)->create(['customer_id' => $user_cgf_1->id]);
    }
}

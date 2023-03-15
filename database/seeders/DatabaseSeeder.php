<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\BudgetLine;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\ProcessType;
use App\Models\ExpenseType;
use App\Models\Certification;
use App\Models\RecordStatus;
use App\Models\Vendor;
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
        // Rol 4: Coordinador General Financiero: actualizar certificación (STEP 4)
        $cgf_coord_role = Role::create(['name' => 'cgf_coord_role']);
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
        $cgf_sec_certification = Permission::create(['name' => 'cgf_sec_certification']);
        // Permiso 6: Datos del Secretario JAPC
        $japc_certification = Permission::create(['name' => 'japc_certification']);
        // Permiso 7: Datos del Analista de Certificación
        $financial_certification = Permission::create(['name' => 'financial_certification']);
        // Permiso 8: Datos del Coordinador General Financiero
        $cgf_coord_certification = Permission::create(['name' => 'cgf_coord_certification']);

        // *---- ASIGNA PERMISOS A ROLES ----*
        $cgf_secretary_role->syncPermissions([
            $create_certification, $show_certification, $update_certification,
            $cgf_sec_certification,
        ]);
        $japc_secretary_role->syncPermissions([
            $show_certification, $update_certification,
            $cgf_sec_certification, $japc_certification,
        ]);
        $certification_analyst_role->syncPermissions([
            $show_certification, $update_certification,
            $cgf_sec_certification, $japc_certification, $financial_certification,
        ]);
        $cgf_coord_role->syncPermissions([
            $show_certification, $update_certification,
            $cgf_sec_certification, $japc_certification,
            $financial_certification, $cgf_coord_certification,

        ]);
        $admin_role->syncPermissions([
            $create_certification, $show_certification, $update_certification, $delete_certification,
            $cgf_sec_certification, $japc_certification, $financial_certification, $cgf_coord_certification,
        ]);

        // *---- DEPARTAMENTOS ----*
        $department = Department::factory(3)->create();

        // *---- TIPOS DE PROCESO ----*
        $process_type = ProcessType::factory(3)->create();

        // *---- TIPOS DE GASTO ----*
        $expense_types = ['PROCESO 2023', 'CONVALIDACION', 'DEUDA DE AÑOS ANTERIORES'];
        foreach ($expense_types as $expense_type) {
            ExpenseType::create(['expense_type' => $expense_type]);
        }

        // *---- PROVEEDORES ----*
        $vendors = ['PROVEEDOR 1', 'PROVEEDOR 2', 'PROVEEDOR 3'];
        foreach ($vendors as $vendor) {
            Vendor::create(['name' => $vendor, 'nit' => '09800000000']);
        }

        // *---- ITEM PRESUPUESTARIOS ----*
        $budget_line = BudgetLine::factory(3)->create();

        // *---- ESTADOS ----*
        $statuses = [
            "PENDIENTE DE REVISIÓN",
            "EN REVISIÓN",
            "REGISTRADO",
            "DEVUELTO",
            "APROBADO",
            "LIQUIDADO",
        ];
        foreach ($statuses as $status) {
            RecordStatus::create(['status' => $status]);
        }

        // *---- USUARIOS ----*
        $user_cgf_1 = User::factory()->create([
            'name' => 'Secretario CGF 1',
            'username' => 'secretariocgf1',
            'email' => 'secretario_cgf_1@gmail.com',
            'department' => 1,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
        ]);
        $user_japc_1 = User::factory()->create([
            'name' => 'Secretario JAPC 1',
            'username' => 'secretariojapc1',
            'email' => 'secretario_japc_1@gmail.com',
            'department' => 2,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',

        ]);
        $user_cert_analyst_1 = User::factory()->create([
            'name' => 'Analista de Certificación 1',
            'username' => 'certif1',
            'email' => 'analista_certificacion_1@gmail.com',
            'department' => 3,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',

        ]);
        $user_cert_analyst_2 = User::factory()->create([
            'name' => 'Analista de Certificación 2',
            'username' => 'certif2',
            'email' => 'analista_certificacion_2@gmail.com',
            'department' => 2,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',

        ]);
        $user_cgf_coord_1 = User::factory()->create([
            'name' => 'Coordinador General Financiero',
            'username' => 'CGF1',
            'email' => 'cgf_coord_1@gmail.com',
            'department' => 1,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
        ]);
        $user_admin = User::factory()->create([
            'name' => 'Root',
            'username' => 'admin',
            'email' => 'root@gmail.com',
            'department' => 2,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
        ]);

        // *---- ASIGNA ROLES A USUARIOS ----*
        $user_cgf_1->assignRole($cgf_secretary_role);
        $user_japc_1->assignRole($japc_secretary_role);
        $user_cert_analyst_1->assignRole($certification_analyst_role);
        $user_cert_analyst_2->assignRole($certification_analyst_role);
        $user_cgf_coord_1->assignRole($cgf_coord_role);
        $user_admin->assignRole($admin_role);

        // Certification::factory(2)->create();
    }
}

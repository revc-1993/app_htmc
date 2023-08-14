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
        $roles = [
            (object) [
                "name" => "cgf_secretary_role",
                "nickname" => "Secretaría CGF"
            ],
            (object) [
                "name" => "japc_secretary_role",
                "nickname" => "Secretaría JAPC"
            ],
            (object) [
                "name" => "analyst_role",
                "nickname" => "Analista"
            ],
            (object) [
                "name" => "cgf_coord_role",
                "nickname" => "Coordinador CGF"
            ],
            (object) [
                "name" => "admin_role",
                "nickname" => "Administrador"
            ],
        ];
        foreach ($roles as $role) {
            ${$role->name} = Role::create([
                'name' => $role->name,
                'nickname' => $role->nickname
            ]);
        }

        // *---- PERMISOS ----*
        $permissions = [
            // Certifications
            'create_certification', 'show_certification', 'update_certification', 'delete_certification',
            // Commitments
            'create_commitment', 'show_commitment', 'update_commitment', 'delete_commitment',
            // Accruals
            'create_accrual', 'show_accrual', 'update_accrual', 'delete_accrual',
            // Currents Management
            'cgf_sec', 'japc', 'financial', 'cgf_coord',
            // Roles
            'create_role', 'show_role', 'update_role', 'delete_role',
            // Users
            'create_user', 'show_user', 'update_user', 'delete_user',
        ];
        foreach ($permissions as $permission) {
            ${$permission} = Permission::create(['name' => $permission]);
        }

        // *---- ASIGNA PERMISOS A ROLES ----*
        $cgf_secretary_role->syncPermissions([
            $create_certification, $show_certification, $update_certification,
            $create_commitment, $show_commitment, $update_commitment,
            $cgf_sec,
        ]);
        $japc_secretary_role->syncPermissions([
            $show_certification, $update_certification,
            $show_commitment, $update_commitment,
            $create_accrual, $show_accrual, $update_accrual,
            $cgf_sec, $japc,
        ]);
        $analyst_role->syncPermissions([
            $show_certification, $update_certification,
            $show_commitment, $update_commitment,
            $show_accrual, $update_accrual,
            $cgf_sec, $japc, $financial,
        ]);
        $cgf_coord_role->syncPermissions([
            $show_certification, $update_certification,
            $show_commitment, $update_commitment,
            $show_accrual, $update_accrual,
            $cgf_sec, $japc, $financial, $cgf_coord,

        ]);
        $admin_role->syncPermissions([
            $create_certification, $show_certification, $update_certification, $delete_certification,
            $create_commitment, $show_commitment, $update_commitment, $delete_commitment,
            $create_accrual, $show_accrual, $update_accrual, $delete_accrual,
            $create_role, $show_role, $update_role, $delete_role,
            $create_user, $show_user, $update_user, $delete_user,
            $cgf_sec, $japc, $financial, $cgf_coord,
        ]);

        // *---- DEPARTAMENTOS ----*
        Department::factory(3)->create();

        // *---- TIPOS DE PROCESO ----*
        ProcessType::factory(3)->create();

        // *---- TIPOS DE GASTO ----*
        $expense_types = ['PROCESO NUEVO', 'CONVALIDACION', 'DEUDA DE AÑOS ANTERIORES'];
        foreach ($expense_types as $expense_type) {
            ExpenseType::create(['expense_type' => $expense_type]);
        }

        // *---- PROVEEDORES ----*
        $vendors = ['PROVEEDOR 1', 'PROVEEDOR 2', 'PROVEEDOR 3'];
        $n = 1;
        foreach ($vendors as $vendor) {
            Vendor::create(['name' => $vendor, 'nit' => '09' . $n . '00000000']);
            $n++;
        }

        // *---- ITEM PRESUPUESTARIOS ----*
        BudgetLine::factory(3)->create();

        // *---- ESTADOS ----*
        $statuses = [
            "PENDIENTE DE REVISIÓN",
            "EN REVISIÓN",
            "OBSERVADO",
            "DEVUELTO",
            "REGISTRADO",
            "APROBADO",
            "ANULADO",
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
        $user_analyst_1 = User::factory()->create([
            'name' => 'Analista de Financiero 1',
            'username' => 'analyst1',
            'email' => 'analista_1@gmail.com',
            'department' => 3,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
        ]);
        $user_analyst_2 = User::factory()->create([
            'name' => 'Analista de Financiero 2',
            'username' => 'analyst2',
            'email' => 'analista_2@gmail.com',
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
        $user_analyst_1->assignRole($analyst_role);
        $user_analyst_2->assignRole($analyst_role);
        $user_cgf_coord_1->assignRole($cgf_coord_role);
        $user_admin->assignRole($admin_role);
    }
}

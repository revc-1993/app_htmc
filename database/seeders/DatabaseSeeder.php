<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
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
        // *---- PERMISOS ----*
        // $this->call(PermissionSeeder::class);

        // *---- GESTION ACTUAL ----*
        $this->call(CurrentManagementSeeder::class);

        // *---- ROLES Y ASIGNACION DE PERMISOS ----*
        $this->call(RoleSeeder::class);

        // *---- DEPARTAMENTOS ----*
        $this->call(DepartmentSeeder::class);

        // *---- TIPOS DE PROCESO ----*
        $this->call(ProcessTypeSeeder::class);

        // *---- ADMINISTRADOR DE CONTRATO ----*
        $this->call(ContractAdministratorSeeder::class);

        // *---- TIPOS DE GASTO ----*
        $this->call(ExpenseTypeSeeder::class);

        // *---- PROVEEDORES ----*
        $this->call(VendorSeeder::class);

        // *---- ITEM PRESUPUESTARIOS ----*
        $this->call(BudgetLineSeeder::class);

        // *---- ESTADOS ----*
        $this->call(RecordStatusSeeder::class);
        $this->call(PaymentStatusSeeder::class);

        // *---- USUARIOS Y ASIGNACION DE ROLES ----*
        $this->call(UserSeeder::class);
    }
}

<?php

// config/roles_and_permissions.php

return [
    'roles' => [
        (object) [
            "name" => "cgf_secretary_role",
            "nickname" => "Secretaría CGF",
            "step" => 1,
        ],
        // RECIBE EXPEDIENTES EN DPTO PRESUPUESTO
        (object) [
            "name" => "japc_secretary_role",
            "nickname" => "Secretaría JAPC",
            "step" => 2,
        ],
        // ANALISTA PARA LA CERTIFICACION
        (object) [
            "name" => "certification_analyst_role",
            "nickname" => "Analista de Certificación",
            "step" => 3,
        ],
        // ANALISTA PARA EL COMPROMISO
        (object) [
            "name" => "commitment_analyst_role",
            "nickname" => "Analista de Compromiso",
            "step" => 3,
        ],
        // ANALISTA PARA EL DEVENGADO
        (object) [
            "name" => "accrual_analyst_role",
            "nickname" => "Analista de Devengado",
            "step" => 3,
        ],
        // APROBADOR DE LAS INSTANCIAS
        (object) [
            "name" => "esgisef_approver_role",
            "nickname" => "Aprobador ESIGEF",
            "step" => 4,
        ],
        // MODULO DE TESORERÍA: JEFE
        (object) [
            "name" => "treasury_manager_role",
            "nickname" => "Jefe de Tesorería",
            "step" => 1,
        ],
        // MODULO DE TESORERÍA: ANALISTA
        (object) [
            "name" => "treasury_analyst_role",
            "nickname" => "Analista de Tesorería",
            "step" => 2,
        ],
        // COORDINADOR FINANCIERO - SOLO LECTURA
        (object) [
            "name" => "coord_cgf_role",
            "nickname" => "Coordinador CGF",
            "step" => 5,
        ],
        // ROOT - TODOS LOS PERIMSOS
        (object) [
            "name" => "admin_role",
            "nickname" => "Root",
            "step" => 5,
        ],
    ],
    'users' => [
        (object) [
            'name' => 'Secretario CGF 1',
            'username' => 'secretario_cgf_1',
            'email' => 'secretario_cgf_1@gmail.com',
            'department' => 1,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
            'role' => 'cgf_secretary_role',
        ],
        (object) [
            'name' => 'Secretario JAPC 1',
            'username' => 'secretario_japc_1',
            'email' => 'secretario_japc_1@gmail.com',
            'department' => 2,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
            'role' => 'japc_secretary_role',
        ],
        (object) [
            'name' => 'Analista de Certificacion 1',
            'username' => 'analyst_cert_1',
            'email' => 'analista_cert_1@gmail.com',
            'department' => 3,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
            'role' => 'certification_analyst_role',
        ],
        (object) [
            'name' => 'Analista de Certificacion 2',
            'username' => 'analyst_cert_2',
            'email' => 'analista_cert_2@gmail.com',
            'department' => 3,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
            'role' => 'certification_analyst_role',
        ],
        (object) [
            'name' => 'Analista de Compromiso 1',
            'username' => 'analyst_comp_1',
            'email' => 'analista_comp_1@gmail.com',
            'department' => 3,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
            'role' => 'commitment_analyst_role',
        ],
        (object) [
            'name' => 'Analista de Devengado 1',
            'username' => 'analyst_dev_1',
            'email' => 'analista_dev_1@gmail.com',
            'department' => 2,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
            'role' => 'accrual_analyst_role',
        ],
        (object) [
            'name' => 'Aprobador ESIGEF 1',
            'username' => 'CGF1',
            'email' => 'cgf_coord_1@gmail.com',
            'department' => 1,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
            'role' => 'esgisef_approver_role',
        ],
        (object) [
            'name' => 'Root',
            'username' => 'admin',
            'email' => 'root@gmail.com',
            'department' => 2,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
            'role' => 'admin_role',
        ],
        (object) [
            'name' => 'Jefe de Tesorería 1',
            'username' => 'treasury_manager_1',
            'email' => 'jefe_tesorería_1@gmail.com',
            'department' => 2,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
            'role' => 'treasury_manager_role',
        ],
        (object) [
            'name' => 'Analista de Tesorería 1',
            'username' => 'treasury_analyst_1',
            'email' => 'analista_tesorería_1@gmail.com',
            'department' => 2,
            'password' => '$2y$10$gwXUxw.oG9eVVSIjezbU3u4.U17Q.2zNt7H3iusa399BkaDgjvGOq',
            'role' => 'treasury_analyst_role',
        ],
    ],
];

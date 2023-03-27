<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware(
            'permission:create_role|show_role|update_role|delete_role',
            ['only' => ['index']]
        );

        $this->middleware(
            'permission:create_role',
            ['only' => ['create', 'store']]
        );

        $this->middleware(
            'permission:update_role',
            ['only' => ['edit', 'update']]
        );

        $this->middleware(
            'permission:delete_role',
            ['only' => ['destroy']]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Roles/Index', [
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get();

        return Inertia::render('Roles/Create', compact('permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->input('name'),
        ]);
        $role->syncPermissions($request->input('permission'));

        $message = [
            "response" => "Registro creado correctamente.",
            "operation" => 1,
        ];

        return to_route('roles.index')->with(compact('message'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::all(['id', 'name'])
            ->where('roles.id', '=', $id)
            ->first();
        $permission = Permission::get();
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_has_permissions.role_id', $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return Inertia::render('Roles/Edit', compact('role', 'permission', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\RoleRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));

        $message = [
            "response" => "Registro modificado correctamente.",
            "operation" => 3,
        ];

        return to_route('roles.index')->with(compact('message'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        $message = [
            "response" => "Registro eliminado correctamente.",
            "operation" => 4,
        ];
        return to_route('roles.index')->with(compact('message'));
    }
}

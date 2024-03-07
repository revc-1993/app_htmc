<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\CustomRole;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin_role');
            })
                ->with([
                    'roles' => function ($query) {
                        $query->select('id', 'name');
                    },
                ])
                ->get(['id', 'name', 'username', 'email', 'department']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('name', '!=', 'admin_role')
            ->get(['id', 'name']);

        return Inertia::render('Users/Create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $names = explode(" ", $data['name']);

        if (count($names) > 1)
            $names[0] = substr($names[0], 0, 1);
        if (count($names) === 3)
            $names[2] = substr($names[2], 0, 1);
        if (count($names) >= 4) {
            $names[1] = "";
            $names[3] = substr($names[3], 0, 1);
        }

        $data['username'] = strtolower(implode("", $names));

        $user = User::create($data);
        $role = CustomRole::findByName($data['role']);
        $user->assignRole($role);

        $message = [
            "response" => "Registro creado correctamente.",
            "operation" => 1,
        ];

        return to_route('superadmin.users.index')->with(compact('message'));
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
        $user = User::where('id', $id)
            ->with([
                'roles' => function ($query) {
                    $query->select('id', 'name')->first();
                },
            ])
            ->first(['id', 'name', 'email', 'department']);

        $roles = Role::where('name', '!=', 'admin_role')
            ->get(['id', 'name']);

        return Inertia::render('Users/Edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data = Arr::except($data, array('password'));
        }

        $names = explode(" ", $data['name']);

        if (count($names) > 1)
            $names[0] = substr($names[0], 0, 1);
        if (count($names) === 3)
            $names[2] = substr($names[2], 0, 1);
        if (count($names) >= 4) {
            $names[1] = "";
            $names[3] = substr($names[3], 0, 1);
        }

        $data['username'] = strtolower(implode("", $names));

        // dd($data['role']);
        $user->update($data);
        DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->delete();
        $role = CustomRole::findByName($data['role']);
        $user->assignRole($role);

        $message = [
            "response" => "Registro modificado correctamente.",
            "operation" => 3,
        ];

        return to_route('superadmin.users.index')->with(compact('message'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        $message = [
            "response" => "Registro eliminado correctamente.",
            "operation" => 4,
        ];
        return to_route('superadmin.users.index')->with(compact('message'));
    }
}

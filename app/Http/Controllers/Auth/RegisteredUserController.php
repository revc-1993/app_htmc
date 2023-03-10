<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'departments' => Department::all(['id', 'department']),
            'role' => Role::all(['id', 'name']),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'department' => 'required',
            'role' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
            'password' => Hash::make($request->password),
        ]);

        // $user->assignRole('')
        $user->assignRole($request->role);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}

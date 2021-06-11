<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            // 'filters' => Request::all('search', 'role', 'trashed'),
            // 'users' => User::filter(Request::only('search', 'role', 'trashed'))
            'filters' => Request::all('search', 'trashed'),
            'users' => User::with('roles')
                ->filter(Request::only('search', 'trashed'))
                ->orderByName()
                ->paginate(),
                // ->map(function ($user) {
                //     return [
                //         'id' => $user->id,
                //         'first_name' => $user->first_name,
                //         'last_name' => $user->last_name,
                //         'email' => $user->email,
                //         // 'owner' => $user->owner,
                //         // 'photo' => $user->photoUrl(['w' => 40, 'h' => 40, 'fit' => 'crop']),
                //         'roles' => $user->getRoleNames(),
                //         'deleted_at' => $user->deleted_at,
                //     ];
                // }),
        ]);
    }

    public function create()
    {
        $roles = Role::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->orderBy('name', 'desc')->get();

        $permissions = Permission::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->orderBy('name', 'desc')->get();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->all());

        if ($request->has('roles')) {
            $user->assignRole($request->roles);
        }

        if ($request->has('permissions')) {
            $user->givePermissionTo($request->permissions);
        }

        return redirect()->route('users.index')->with('success', 'User was successfully added.');
    }

    public function edit(User $user)
    {
        $roles = Role::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->orderBy('name', 'desc')->get();

        $permissions = Permission::when(request('term'), function($query, $term) {
            $query->where('name', 'like', "%$term%");
        })->orderBy('name', 'desc')->get();

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                // 'owner' => $user->owner,
                // 'photo' => $user->photoUrl(['w' => 60, 'h' => 60, 'fit' => 'crop']),
                'selected_roles' => $user->roles,
                'selected_permissions' => $user->permissions,
                'deleted_at' => $user->deleted_at,
            ],
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function update(StoreUserRequest $request, User $user)
    {
        // if (App::environment('demo') && $user->isDemoUser()) {
        //     return Redirect::back()->with('error', 'Updating the demo user is not allowed.');
        // }

        $user->update($request->only('first_name', 'last_name', 'email'));

        // if (Request::file('photo')) {
        //     $user->update(['photo_path' => Request::file('photo')->store('users')]);
        // }

        if (Request::get('password')) {
            $user->update(['password' => Request::get('password')]);
        }

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // if (App::environment('demo') && $user->isDemoUser()) {
        //     return Redirect::back()->with('error', 'Deleting the demo user is not allowed.');
        // }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }

    public function restore(User $user)
    {
        $user->restore();

        return redirect()->route('users.index')->with('success', 'User restored.');
    }
}
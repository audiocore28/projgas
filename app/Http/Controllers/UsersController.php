<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'filters' => Request::all('search', 'role', 'trashed'),
            'users' => User::paginate(5)
                // ->orderByName()
                // ->filter(Request::only('search', 'role', 'trashed'))
                // ->get()
                // ->transform(function ($user) {
                //     return [
                //         'id' => $user->id,
                //         'name' => $user->name,
                //         'email' => $user->email,
                //         'owner' => $user->owner,
                //         'photo' => $user->photoUrl(['w' => 40, 'h' => 40, 'fit' => 'crop']),
                //         'deleted_at' => $user->deleted_at,
                //     ];
                // }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request)
    {
        User::create(
            request()->validate([
                'name' => ['required', 'max:50'],
                'email' => ['required', 'max:50', 'email', Rule::unique('users')],
                'password' => ['nullable'],
                // 'owner' => ['required', 'boolean'],
                // 'photo' => ['nullable', 'image'],
            ])
        );

        return redirect()->route('users.index')->with('success', 'User was successfully added.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                // 'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => $user->password,
                // 'owner' => $user->owner,
                // 'photo' => $user->photoUrl(['w' => 60, 'h' => 60, 'fit' => 'crop']),
                // 'deleted_at' => $user->deleted_at,
            ],
        ]);
    }

    public function update(User $user)
    {
        // if (App::environment('demo') && $user->isDemoUser()) {
        //     return Redirect::back()->with('error', 'Updating the demo user is not allowed.');
        // }

        $user->update(
            request()->validate([
                'name' => ['max:50'],
                'email' => ['max:50', 'email', Rule::unique('users')],
                'password' => ['nullable'],
            ])
        );

        // if (Request::file('photo')) {
        //     $user->update(['photo_path' => Request::file('photo')->store('users')]);
        // }

        if (Request::get('password')) {
            $user->update(['password' => Request::get('password')]);
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
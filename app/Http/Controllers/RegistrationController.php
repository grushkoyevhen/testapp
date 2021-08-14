<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\TestRequest;
use App\Models\User;

class RegistrationController extends Controller
{
    public function index()
    {
        return view('registration');
    }

    public function create(CreateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->save();

        return redirect()->route('auth.index');
    }
}

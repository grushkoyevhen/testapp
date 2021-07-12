<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegController extends Controller
{
    public function index() {
        return view('reg');
    }

    public function create(Request $request, User $user) {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();

        return redirect()->route('auth.index');
    }
}

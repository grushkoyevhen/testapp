<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function index() {
        return view('auth');
    }

    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'email' => 'bail|required|exists:users',
            'password' => 'required',
            'remember' => 'in:1'
        ]);

        $remember = (!isset($credentials['remember']) )? false : true;

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->regenerate();
        return redirect()->route('auth.index');
    }
}

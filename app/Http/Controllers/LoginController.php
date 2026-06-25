<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (session()->has('user_id')) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->identifier)
            ->orWhere('nama', $request->identifier)
            ->first();

        if (!$user) {
            return back()->withErrors(['type' => 'username_not_found'])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['type' => 'password_invalid'])->withInput();
        }

        session([
            'user_id' => $user->id,
            'user_name' => $user->nama,
            'user_role' => $user->role,
        ]);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}

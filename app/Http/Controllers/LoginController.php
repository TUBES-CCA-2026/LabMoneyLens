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

    public function login(\App\Http\Requests\LoginRequest $request)
    {
        $service = app(\App\Services\AuthService::class);
        $result = $service->attemptLogin($request->input('identifier'), $request->input('password'));

        if (! $result['success']) {
            return back()->withErrors(['type' => $result['error']])->withInput();
        }

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        $service = app(\App\Services\AuthService::class);
        $service->logout();

        return redirect()->route('login');
    }
}

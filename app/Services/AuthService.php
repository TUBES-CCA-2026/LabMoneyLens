<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function attemptLogin(string $identifier, string $password)
    {
        $user = User::where('email', $identifier)
            ->orWhere('nama', $identifier)
            ->first();

        if (!$user) {
            return ['success' => false, 'error' => 'username_not_found'];
        }

        if (!Hash::check($password, $user->password)) {
            return ['success' => false, 'error' => 'password_invalid'];
        }

        session([
            'user_id' => $user->id,
            'user_name' => $user->nama,
            'user_role' => $user->role,
            'user_photo' => $user->foto_profil,
        ]);

        return ['success' => true];
    }

    public function logout()
    {
        session()->flush();
    }
}

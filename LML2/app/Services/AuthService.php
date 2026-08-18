<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function attemptLogin(string $identifier, string $password): array
    {
        $user = User::where('email', $identifier)
            ->orWhere('nama', $identifier)
            ->first();

        // Keep authentication failure responses intentionally generic so the
        // login endpoint does not reveal whether an account exists.
        if (! $user || ! Hash::check($password, $user->password)) {
            return ['success' => false, 'error' => 'authentication_failed'];
        }

        // Prevent session fixation after successful authentication.
        session()->regenerate();

        session([
            'user_id' => $user->id,
            'user_name' => $user->nama,
            'user_role' => $user->role,
            'user_photo' => $user->foto_profil,
        ]);

        return ['success' => true];
    }

    public function logout(): void
    {
        session()->invalidate();
        session()->regenerateToken();
    }
}

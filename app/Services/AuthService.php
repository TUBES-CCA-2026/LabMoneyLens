<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
<<<<<<< HEAD
    public function attemptLogin(string $identifier, string $password)
=======
    public function attemptLogin(string $identifier, string $password): array
>>>>>>> 0026227 (Baru)
    {
        $user = User::where('email', $identifier)
            ->orWhere('nama', $identifier)
            ->first();

<<<<<<< HEAD
        if (!$user) {
            return ['success' => false, 'error' => 'username_not_found'];
        }

        if (!Hash::check($password, $user->password)) {
            return ['success' => false, 'error' => 'password_invalid'];
        }
=======
        // Keep authentication failure responses intentionally generic so the
        // login endpoint does not reveal whether an account exists.
        if (! $user || ! Hash::check($password, $user->password)) {
            return ['success' => false, 'error' => 'authentication_failed'];
        }

        // Prevent session fixation after successful authentication.
        session()->regenerate();
>>>>>>> 0026227 (Baru)

        session([
            'user_id' => $user->id,
            'user_name' => $user->nama,
            'user_role' => $user->role,
            'user_photo' => $user->foto_profil,
        ]);

        return ['success' => true];
    }

<<<<<<< HEAD
    public function logout()
    {
        session()->flush();
=======
    public function logout(): void
    {
        session()->invalidate();
        session()->regenerateToken();
>>>>>>> 0026227 (Baru)
    }
}

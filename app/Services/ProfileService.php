<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function updateProfile(array $data, ?\Illuminate\Http\UploadedFile $photo = null)
    {
        $user = User::findOrFail(session('user_id'));

        $user->nama = $data['nama'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($photo) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $user->foto_profil = $photo->store('profile', 'public');
        }

        $user->save();

        session([
            'user_name' => $user->nama,
            'user_photo' => $user->foto_profil,
        ]);

        return ['success' => true];
    }
}

<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileService
{
    public function updateProfile(array $data, ?\Illuminate\Http\UploadedFile $photo = null, ?string $base64Photo = null)
    {
        $user = User::findOrFail(session('user_id'));

        $user->nama = $data['nama'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // Prioritas: base64 hasil crop lingkaran
        if ($base64Photo && str_starts_with($base64Photo, 'data:image')) {
            // Hapus foto lama
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            // Decode base64
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Photo);
            $imageData = base64_decode($imageData);

            $filename = 'profile/' . Str::uuid() . '.png';
            Storage::disk('public')->put($filename, $imageData);

            $user->foto_profil = $filename;
        } elseif ($photo) {
            // Fallback: upload file biasa
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


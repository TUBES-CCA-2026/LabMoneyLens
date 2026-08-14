<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $user = User::findOrFail(session('user_id'));
        
        // Sinkronisasi session dengan data database terbaru
        session([
            'user_name' => $user->nama,
            'user_photo' => $user->foto_profil,
        ]);

        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'foto_profil_cropped' => ['nullable', 'string'],
        ]);

        $service = app(\App\Services\ProfileService::class);
        $service->updateProfile(
            $request->only(['nama', 'password']),
            null,
            $request->input('foto_profil_cropped')
        );

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}

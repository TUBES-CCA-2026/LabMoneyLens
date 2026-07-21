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
            'foto_profil' => ['nullable', 'image', 'max:2048'],
        ]);

        $service = app(\App\Services\ProfileService::class);
        $service->updateProfile($request->only(['nama', 'password']), $request->file('foto_profil'));

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}

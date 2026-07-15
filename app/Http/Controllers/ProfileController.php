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

        $user = User::findOrFail(session('user_id'));

        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'foto_profil' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->nama = $request->nama;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $user->foto_profil = $request->file('foto_profil')->store('profile', 'public');
        }

        $user->save();

        session([
            'user_name' => $user->nama,
            'user_photo' => $user->foto_profil,
        ]);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}

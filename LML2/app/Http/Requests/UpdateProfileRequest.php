<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return session()->has('user_id');
    }

    public function rules()
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'foto_profil' => ['nullable', 'image', 'max:2048'],
        ];
    }
}

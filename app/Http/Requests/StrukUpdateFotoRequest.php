<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StrukUpdateFotoRequest extends FormRequest
{
    public function authorize()
    {
        return session()->has('user_id') && session('user_role') !== 'Kepala Lab';
    }

    public function rules()
    {
        return [
            'foto_baru' => 'required|image|max:5120',
        ];
    }
}

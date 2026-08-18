<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengeluaranRequest extends FormRequest
{
    public function authorize()
    {
        return session()->has('user_id') && session('user_role') !== 'Kepala Lab';
    }

    public function rules()
    {
        return [
            'tanggal'               => 'required|date',
            'id_pengeluaran'        => 'array',
            'uraian'                => 'array',
            'uraian.*'              => 'nullable|string|max:255',
            'nominal'               => 'required|array',
            'nominal.*'             => 'required|numeric|min:0',
            'kuantiti'              => 'array',
            'kuantiti.*'            => 'nullable|integer|min:1',
<<<<<<< HEAD
            'id_jenis_pengeluaran'  => 'required|integer',
=======
            'id_jenis_pengeluaran'  => 'required|integer|exists:jenis_pengeluaran,id_jenis_pengeluaran',
>>>>>>> 0026227 (Baru)
        ];
    }
}

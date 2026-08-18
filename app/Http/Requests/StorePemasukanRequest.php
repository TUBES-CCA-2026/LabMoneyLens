<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePemasukanRequest extends FormRequest
{
    public function authorize()
    {
        return session()->has('user_id') && session('user_role') !== 'Kepala Lab';
    }

    public function rules()
    {
        return [
            'tanggal' => 'required|date',
            'uraian' => 'array',
            'uraian.*' => 'nullable|string|max:255',
            'nominal' => 'required|array',
            'nominal.*' => 'required|numeric|min:0',
<<<<<<< HEAD
            'id_jenis_penerimaan' => 'required|array',
            'id_jenis_penerimaan.*' => 'required|integer',
=======
            'kuantiti' => 'array',
            'kuantiti.*' => 'nullable|integer|min:1',
            'id_jenis_penerimaan' => 'required|array',
            'id_jenis_penerimaan.*' => 'required|integer|exists:jenis_penerimaan,id_jenis_penerimaan',
>>>>>>> 0026227 (Baru)
            'receipt_image' => 'required|image|max:5120',
        ];
    }
}

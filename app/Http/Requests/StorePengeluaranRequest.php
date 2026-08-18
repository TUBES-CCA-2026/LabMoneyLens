<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengeluaranRequest extends FormRequest
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
            'nominal.*' => 'required|numeric|min:1',
            'kuantiti' => 'array',
            'kuantiti.*' => 'nullable|integer|min:1',
            'id_jenis_pengeluaran' => 'required|array',
<<<<<<< HEAD
            'id_jenis_pengeluaran.*' => 'required|integer',
=======
            'id_jenis_pengeluaran.*' => 'required|integer|exists:jenis_pengeluaran,id_jenis_pengeluaran',
>>>>>>> 0026227 (Baru)
            'receipt_image' => 'required|image|max:5120',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiptAnalysisRequest extends FormRequest
{
    public function authorize()
    {
        return session()->has('user_id');
    }

    public function rules()
    {
        return [
            'receipt_image' => 'required|image|max:5120',
            'type' => 'required|in:pemasukan,pengeluaran',
        ];
    }
}

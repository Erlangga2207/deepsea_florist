<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PembayaranRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'jumlah' => ['required', 'numeric', 'gt:0'],
            'metode' => ['required', Rule::in(['transfer', 'qris', 'cash'])],
            'tanggal' => ['required', 'date'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\Pesanan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UbahStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(Pesanan::STATUS))],
            'masuk_stok_jadi' => ['boolean'],
            'harga_jual' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

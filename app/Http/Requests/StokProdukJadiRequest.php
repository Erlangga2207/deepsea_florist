<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StokProdukJadiRequest extends FormRequest
{
    public function rules(): array
    {
        // Mengubah (tandai terjual) cukup harga; menambah butuh model atau nama
        if ($this->isMethod('put')) {
            return ['harga_jual' => ['nullable', 'numeric', 'min:0']];
        }

        return [
            'produk_id' => ['nullable', 'exists:produk,id'],
            'nama' => ['nullable', 'required_without:produk_id', 'string', 'max:150'],
            'harga_jual' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

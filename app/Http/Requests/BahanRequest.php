<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BahanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'jenis' => ['required', Rule::in(['artificial', 'fresh', 'pendukung'])],
            'satuan' => ['required', 'string', 'max:20'],
            'stok_minimum' => ['required', 'numeric', 'min:0'],
            'harga_beli_terakhir' => ['required', 'numeric', 'min:0'],
            'dijual_eceran' => ['boolean'],
            'harga_eceran' => ['nullable', 'required_if:dijual_eceran,true', 'numeric', 'min:0'],
            'is_aktif' => ['boolean'],
            // Hanya di form tambah; dicatat sebagai mutasi masuk
            'stok_awal' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'dijual_eceran' => $this->boolean('dijual_eceran'),
            'is_aktif' => $this->boolean('is_aktif'),
        ]);
    }
}

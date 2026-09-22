<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CekPesananRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'produk_id' => ['nullable', 'exists:produk,id'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:999'],
            'tanggal_jadi' => ['nullable', 'date'],
            'jumlah_lembar' => ['nullable', 'numeric', 'min:0'],
            'tarif_lipat' => ['nullable', 'numeric', 'min:0'],
            'sewa_rangka' => ['nullable', 'numeric', 'min:0'],
            'ongkos_pasang' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

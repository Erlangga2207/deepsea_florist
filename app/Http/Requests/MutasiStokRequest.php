<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MutasiStokRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bahan_id' => ['required', 'exists:bahan,id'],
            'tipe' => ['required', Rule::in(['masuk', 'keluar', 'penyesuaian'])],
            // Untuk penyesuaian, "jumlah" berarti stok hasil hitung fisik, jadi boleh 0
            'jumlah' => ['required', 'numeric', $this->input('tipe') === 'penyesuaian' ? 'min:0' : 'gt:0'],
            'tanggal' => ['required', 'date'],
            'tanggal_kadaluarsa' => ['nullable', 'date', 'after_or_equal:tanggal'],
            'harga_beli' => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'required_if:tipe,penyesuaian', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'keterangan.required_if' => 'Tulis alasan penyesuaian, misalnya "bunga layu" atau "hasil hitung ulang".',
        ];
    }
}

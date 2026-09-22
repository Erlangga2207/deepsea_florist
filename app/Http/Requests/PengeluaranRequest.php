<?php

namespace App\Http\Requests;

use App\Models\Pengeluaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PengeluaranRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'kategori' => ['required', Rule::in(array_keys(Pengeluaran::KATEGORI))],
            'nominal' => ['required', 'numeric', 'gt:0'],
            'keterangan' => ['nullable', 'string', 'max:200'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
        ];
    }
}

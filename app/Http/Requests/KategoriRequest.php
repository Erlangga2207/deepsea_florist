<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', 'alpha_dash', Rule::unique('kategori')->ignore($this->route('kategori'))],
            'tipe_harga' => ['required', Rule::in(['artificial', 'fresh', 'uang', 'papan', 'umum'])],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'meta_deskripsi' => ['nullable', 'string', 'max:160'],
        ];
    }
}

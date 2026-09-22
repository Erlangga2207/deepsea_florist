<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProdukRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kategori_id' => ['required', 'exists:kategori,id'],
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'meta_deskripsi' => ['nullable', 'string', 'max:160'],
            'harga_dasar' => ['nullable', 'numeric', 'min:0'],
            'tampilkan_harga' => ['boolean'],
            'estimasi_jam' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'faktor_kerumitan' => ['required', 'integer', 'between:1,3'],
            'status' => ['required', Rule::in(['ready', 'preorder'])],
            'is_aktif' => ['boolean'],
            'foto_utama' => ['nullable', 'image', 'max:4096'],
            'galeri' => ['nullable', 'array', 'max:8'],
            'galeri.*' => ['image', 'max:4096'],
        ];
    }

    // Checkbox yang tidak dicentang tidak terkirim sama sekali
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tampilkan_harga' => $this->boolean('tampilkan_harga'),
            'is_aktif' => $this->boolean('is_aktif'),
            'estimasi_jam' => $this->filled('estimasi_jam') ? $this->input('estimasi_jam') : 0,
        ]);
    }
}

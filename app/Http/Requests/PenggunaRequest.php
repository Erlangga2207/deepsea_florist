<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PenggunaRequest extends FormRequest
{
    public function rules(): array
    {
        $pengguna = $this->route('pengguna');

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($pengguna)],
            'role' => ['required', Rule::in(['owner', 'karyawan'])],
            'is_aktif' => ['boolean'],
            // Saat mengubah, kosongkan kata sandi bila tidak ingin menggantinya
            'password' => [$pengguna ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_aktif' => $this->boolean('is_aktif')]);
    }

    // Owner tidak boleh mengunci dirinya sendiri keluar dari panel
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->route('pengguna')?->is($this->user())) {
                    if ($this->input('role') !== 'owner') {
                        $validator->errors()->add('role', 'Anda tidak bisa menurunkan peran akun sendiri.');
                    }
                    if (! $this->boolean('is_aktif')) {
                        $validator->errors()->add('is_aktif', 'Anda tidak bisa menonaktifkan akun sendiri.');
                    }
                }
            },
        ];
    }
}

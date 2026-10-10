<?php

namespace App\Http\Requests;

use App\Models\Pengaturan;
use Illuminate\Foundation\Http\FormRequest;

class PengaturanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama_toko' => ['required', 'string', 'max:150'],
            'jam_buka' => ['nullable', 'string', 'max:100'],
            'no_wa' => ['required', 'regex:/^62\d{8,13}$/'],
            'link_ig' => ['nullable', 'url', 'max:255'],
            'link_tiktok' => ['nullable', 'url', 'max:255'],
            'tentang' => ['nullable', 'string'],
            'jumlah_perakit' => ['required', 'integer', 'min:1', 'max:50'],
            'jam_kerja_per_hari' => ['required', 'numeric', 'gt:0', 'max:24'],
            'margin_default' => ['required', 'numeric', 'min:1', 'max:10'],
            'tarif_jasa_per_jam' => ['required', 'numeric', 'min:0'],
            'meta_judul_default' => ['nullable', 'string', 'max:70'],
            'meta_deskripsi_default' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function messages(): array
    {
        return [
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
            'no_wa.regex' => 'Nomor WhatsApp tidak valid. Contoh: 0812 9683 6363 atau 6281296836363.',
        ];
    }

    // Owner biasa menulis 0812… atau +62 812…, ubah otomatis jadi 62812…
    protected function prepareForValidation(): void
    {
        $this->merge(['no_wa' => Pengaturan::normalisasiWa($this->input('no_wa'))]);
    }
}

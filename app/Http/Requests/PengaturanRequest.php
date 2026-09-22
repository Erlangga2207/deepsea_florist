<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengaturanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama_toko' => ['required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string'],
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
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'google_maps_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'no_wa.regex' => 'Tulis nomor WhatsApp diawali 62 tanpa spasi atau tanda +, contoh 6281296836363.',
        ];
    }

    // Owner biasa menulis 0812…, ubah otomatis jadi 62812…
    protected function prepareForValidation(): void
    {
        $wa = preg_replace('/\D/', '', (string) $this->input('no_wa'));
        if (str_starts_with($wa, '0')) {
            $wa = '62'.substr($wa, 1);
        }

        $this->merge(['no_wa' => $wa]);
    }
}

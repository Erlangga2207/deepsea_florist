<?php

namespace App\Http\Requests;

use App\Models\Cabang;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CabangRequest extends FormRequest
{
    private const HOST_MAPS = ['maps.google.com', 'maps.app.goo.gl'];

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
            'koordinat' => ['required', 'regex:/^-?\d{1,3}(\.\d+)?,-?\d{1,3}(\.\d+)?$/'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'url_maps' => ['nullable', 'string', 'max:255', $this->urlMapsValid(...)],
            'no_wa' => ['nullable', 'regex:/^62\d{8,13}$/'],
            'jam_buka' => ['nullable', 'string', 'max:150'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_utama' => ['boolean'],
            'tampil_di_website' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'koordinat.required' => 'Koordinat wajib diisi supaya pin peta muncul.',
            'koordinat.regex' => 'Format koordinat salah. Tulis dua angka dipisah koma, contoh: -6.5312, 107.7234.',
            'latitude.between' => 'Angka pertama (latitude) harus antara -90 dan 90. Pastikan urutannya latitude, lalu longitude.',
            'longitude.between' => 'Angka kedua (longitude) harus antara -180 dan 180.',
            'no_wa.regex' => 'Nomor WhatsApp tidak valid. Contoh: 0812 9683 6363 atau 6281296836363.',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $cabang = $this->route('cabang');
            $utama = $this->boolean('is_utama') || $cabang?->is_utama;

            if ($cabang?->is_utama && ! $this->boolean('is_utama')) {
                $validator->errors()->add('is_utama', 'Harus selalu ada satu cabang utama. Jadikan cabang lain utama, nanti cabang ini otomatis dilepas.');
            }
            if ($utama && ! $this->boolean('tampil_di_website')) {
                $validator->errors()->add('tampil_di_website', 'Cabang utama tidak boleh disembunyikan dari website.');
            }
        }];
    }

    // "-6.5312, 107.7234" dari Google Maps → latitude & longitude
    protected function prepareForValidation(): void
    {
        $koordinat = preg_replace('/\s+/', '', (string) $this->input('koordinat'));
        [$lat, $lng] = array_pad(explode(',', $koordinat, 2), 2, null);
        $formatBenar = preg_match('/^-?\d{1,3}(\.\d+)?,-?\d{1,3}(\.\d+)?$/', $koordinat);

        $this->merge([
            'koordinat' => $koordinat,
            'latitude' => $formatBenar ? $lat : null,
            'longitude' => $formatBenar ? $lng : null,
            'no_wa' => Cabang::normalisasiWa($this->input('no_wa')),
            'url_maps' => trim((string) $this->input('url_maps')) ?: null,
            'is_utama' => $this->boolean('is_utama'),
            'tampil_di_website' => $this->boolean('tampil_di_website'),
        ]);
    }

    private function urlMapsValid(string $atribut, mixed $nilai, Closure $gagal): void
    {
        $url = parse_url($nilai);
        $host = strtolower($url['host'] ?? '');
        $path = $url['path'] ?? '';

        $sah = ($url['scheme'] ?? '') === 'https' && (
            in_array($host, self::HOST_MAPS)
            || (in_array($host, ['google.com', 'www.google.com', 'goo.gl']) && preg_match('#^/maps(/|$)#', $path))
        );

        if (! $sah) {
            $gagal('Link Google Maps harus diawali https:// dan berasal dari google.com/maps, maps.google.com, goo.gl/maps, atau maps.app.goo.gl.');
        }
    }
}

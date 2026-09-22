<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Ubah data umum pesanan. Model & jumlah tidak bisa diubah di sini karena terkait stok bahan.
class PesananUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama_pelanggan' => ['required', 'string', 'max:100'],
            'kontak' => ['nullable', 'string', 'max:50'],
            'sumber' => ['required', Rule::in(['wa', 'ig', 'tiktok', 'langsung'])],
            'ukuran' => ['nullable', 'string', 'max:50'],
            'warna' => ['nullable', 'string', 'max:50'],
            'kartu_ucapan' => ['nullable', 'string', 'max:500'],
            'harga' => ['required', 'numeric', 'min:0'],
            'tanggal_jadi' => ['required', 'date'],
            'metode_ambil' => ['required', Rule::in(['ambil', 'antar'])],
            'alamat_antar' => ['nullable', 'string', 'max:500'],
            'ongkir' => ['nullable', 'numeric', 'min:0'],
            'biaya_tambahan' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

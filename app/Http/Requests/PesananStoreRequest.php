<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Sengaja longgar: hanya nama, model, harga, dan tanggal jadi yang wajib.
class PesananStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama_pelanggan' => ['required', 'string', 'max:100'],
            'kontak' => ['nullable', 'string', 'max:50'],
            'sumber' => ['required', Rule::in(['wa', 'ig', 'tiktok', 'langsung'])],
            'produk_id' => ['nullable', 'exists:produk,id'],
            'nama_item' => ['nullable', 'required_without:produk_id', 'string', 'max:150'],
            'ukuran' => ['nullable', 'string', 'max:50'],
            'warna' => ['nullable', 'string', 'max:50'],
            'kartu_ucapan' => ['nullable', 'string', 'max:500'],
            'qty' => ['required', 'integer', 'min:1', 'max:999'],
            'harga' => ['required', 'numeric', 'min:0'],
            'tanggal_jadi' => ['required', 'date', 'after_or_equal:today'],
            'metode_ambil' => ['required', Rule::in(['ambil', 'antar'])],
            'alamat_antar' => ['nullable', 'string', 'max:500'],
            'ongkir' => ['nullable', 'numeric', 'min:0'],
            'biaya_tambahan' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'dp_jumlah' => ['nullable', 'numeric', 'min:0'],
            'dp_metode' => ['nullable', 'required_with:dp_jumlah', Rule::in(['transfer', 'qris', 'cash'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_item.required_without' => 'Pilih model dari katalog, atau tulis nama model custom.',
            'tanggal_jadi.after_or_equal' => 'Tanggal jadi tidak boleh sebelum hari ini.',
        ];
    }
}

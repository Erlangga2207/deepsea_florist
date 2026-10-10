<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PembayaranRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'jumlah' => ['required', 'integer', 'min:1', 'max:'.$this->route('pesanan')->sisaTagihan()],
            'metode' => ['required', Rule::in(['transfer', 'qris', 'cash'])],
            'tanggal' => ['required', 'date'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $pesanan = $this->route('pesanan');
            $pesan = match (true) {
                $pesanan->status === 'batal' => 'Pesanan batal tidak menerima pembayaran.',
                $pesanan->sisaTagihan() <= 0 => 'Pesanan ini sudah lunas.',
                $pesanan->pembayaran()->count() >= 2 => 'Sudah dua kali bayar. Maksimal dua kali pembayaran per pesanan.',
                default => null,
            };
            if ($pesan) {
                $validator->errors()->add('jumlah', $pesan);
            }
        }];
    }

    public function messages(): array
    {
        return [
            'jumlah.required' => 'Jumlah bayar wajib diisi.',
            'jumlah.integer' => 'Jumlah bayar harus bilangan bulat rupiah.',
            'jumlah.min' => 'Jumlah bayar minimal Rp 1.',
            'jumlah.max' => 'Jumlah melebihi sisa tagihan Rp '.number_format($this->route('pesanan')->sisaTagihan(), 0, ',', '.').'.',
            'metode.required' => 'Pilih cara bayar.',
            'metode.in' => 'Cara bayar tidak dikenal.',
        ];
    }
}

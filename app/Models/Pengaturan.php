<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Hanya satu baris (id = 1).
class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'jumlah_perakit' => 'integer',
            'jam_kerja_per_hari' => 'decimal:2',
            'margin_default' => 'decimal:2',
            'tarif_jasa_per_jam' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public static function ambil(): self
    {
        return static::findOrFail(1);
    }

    // Link wa.me biasa (bukan API WhatsApp), pesan sudah di-encode
    public function linkWa(string $pesan = 'Halo Deepsea Florist, saya mau tanya buket.'): string
    {
        return 'https://wa.me/'.$this->no_wa.'?text='.rawurlencode($pesan);
    }

    // Koordinat kalau sudah diisi. Kalau belum, pakai alamat tanpa RT/RW
    // (Google tidak mengenali RT/RW, petanya jadi kosong). Titiknya baru setingkat jalan.
    public function lokasiPeta(): string
    {
        return $this->latitude && $this->longitude
            ? $this->latitude.','.$this->longitude
            : preg_replace('#\s*RT\.?\s*\d+\s*/\s*RW\.?\s*\d+,?#i', '', $this->alamat);
    }

    public static function pesanProduk(Produk $produk): string
    {
        return "Halo Deepsea Florist, saya mau tanya {$produk->nama} ({$produk->kode}) yang ada di website. Untuk tanggal …";
    }
}

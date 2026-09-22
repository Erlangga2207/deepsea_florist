<?php

namespace App\Domain\Harga;

use App\Models\Pengaturan;
use App\Models\Produk;

// Uangnya milik pelanggan, bukan modal toko. Yang dihitung hanya ongkos melipat per lembar.
class HargaUang extends KalkulatorHarga
{
    public function __construct(
        Produk $produk,
        Pengaturan $pengaturan,
        protected float $jumlahLembar = 0,
        protected float $tarifLipat = 0
    ) {
        parent::__construct($produk, $pengaturan);
    }

    public function hitungHarga(): float
    {
        return ($this->jumlahLembar * $this->tarifLipat) + $this->ongkosJasa();
    }

    public function labelKategori(): string
    {
        return 'Buket uang';
    }

    public function rumus(): string
    {
        return '(jumlah lembar × tarif lipat) + ongkos jasa';
    }

    public function rincianTambahan(): array
    {
        return ["Lipat {$this->jumlahLembar} lembar × Rp ".number_format($this->tarifLipat, 0, ',', '.') => $this->jumlahLembar * $this->tarifLipat];
    }
}

<?php

namespace App\Domain\Harga;

use App\Models\Pengaturan;
use App\Models\Produk;

// Bunga papan: ada sewa rangka dan ongkos pasang di lokasi, bahannya tidak dikali margin.
class HargaPapan extends KalkulatorHarga
{
    public function __construct(
        Produk $produk,
        Pengaturan $pengaturan,
        protected float $sewaRangka = 0,
        protected float $ongkosPasang = 0
    ) {
        parent::__construct($produk, $pengaturan);
    }

    public function hitungHarga(): float
    {
        return $this->sewaRangka + $this->modalBahan() + $this->ongkosPasang + $this->ongkosJasa();
    }

    public function labelKategori(): string
    {
        return 'Bunga papan';
    }

    public function rumus(): string
    {
        return 'sewa rangka + modal bahan + ongkos pasang + ongkos jasa';
    }

    public function rincianTambahan(): array
    {
        return [
            'Sewa rangka' => $this->sewaRangka,
            'Ongkos pasang di lokasi' => $this->ongkosPasang,
        ];
    }
}

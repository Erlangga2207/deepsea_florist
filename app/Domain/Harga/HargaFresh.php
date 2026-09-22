<?php

namespace App\Domain\Harga;

// Bunga fresh bisa layu sebelum terjual, jadi ada cadangan risiko 10%.
class HargaFresh extends KalkulatorHarga
{
    public const CADANGAN_LAYU = 1.10;

    public function hitungHarga(): float
    {
        return ($this->modalBahan() * $this->margin() * self::CADANGAN_LAYU) + $this->ongkosJasa();
    }

    public function labelKategori(): string
    {
        return 'Buket fresh';
    }

    public function rumus(): string
    {
        return '(modal bahan × margin × 1,10) + ongkos jasa';
    }

    public function rincianTambahan(): array
    {
        $cadangan = $this->modalBahan() * $this->margin() * (self::CADANGAN_LAYU - 1);

        return ['Cadangan risiko layu 10%' => $cadangan];
    }
}

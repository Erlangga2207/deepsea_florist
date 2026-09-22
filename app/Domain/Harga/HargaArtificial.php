<?php

namespace App\Domain\Harga;

class HargaArtificial extends KalkulatorHarga
{
    public function hitungHarga(): float
    {
        return ($this->modalBahan() * $this->margin()) + $this->ongkosJasa();
    }

    public function labelKategori(): string
    {
        return 'Buket artificial';
    }

    public function rumus(): string
    {
        return '(modal bahan × margin) + ongkos jasa';
    }
}

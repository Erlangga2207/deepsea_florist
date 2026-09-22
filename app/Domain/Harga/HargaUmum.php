<?php

namespace App\Domain\Harga;

// Cadangan untuk kategori yang belum punya rumus khusus (buket snack, dekorasi).
class HargaUmum extends KalkulatorHarga
{
    public function hitungHarga(): float
    {
        return ($this->modalBahan() * $this->margin()) + $this->ongkosJasa();
    }

    public function labelKategori(): string
    {
        return 'Umum';
    }

    public function rumus(): string
    {
        return '(modal bahan × margin) + ongkos jasa';
    }
}

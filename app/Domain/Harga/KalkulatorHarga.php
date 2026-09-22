<?php

namespace App\Domain\Harga;

use App\Models\Pengaturan;
use App\Models\Produk;

// Kelas induk: setiap produk pasti bisa dihitung harganya, caranya ditentukan turunan.
abstract class KalkulatorHarga
{
    public function __construct(
        protected Produk $produk,
        protected Pengaturan $pengaturan
    ) {
        $this->produk->loadMissing('komposisi.bahan');
    }

    // Sama untuk semua kategori
    public function modalBahan(): float
    {
        $total = 0;
        foreach ($this->produk->komposisi as $item) {
            $total += $item->jumlah * $item->bahan->harga_beli_terakhir;
        }

        return $total;
    }

    public function ongkosJasa(): float
    {
        return $this->produk->estimasi_jam
             * $this->pengaturan->tarif_jasa_per_jam
             * $this->produk->faktor_kerumitan;
    }

    public function margin(): float
    {
        return (float) $this->pengaturan->margin_default;
    }

    // Wajib ditulis ulang tiap turunan
    abstract public function hitungHarga(): float;

    abstract public function labelKategori(): string;

    // Rumus dalam kalimat, untuk ditampilkan ke pemilik
    abstract public function rumus(): string;

    // Baris hitungan khusus kategori (mis. sewa rangka). Turunan boleh menimpa.
    public function rincianTambahan(): array
    {
        return [];
    }

    public function rincian(): array
    {
        return [
            'label' => $this->labelKategori(),
            'rumus' => $this->rumus(),
            'modal_bahan' => $this->modalBahan(),
            'margin' => $this->margin(),
            'ongkos_jasa' => $this->ongkosJasa(),
            'tambahan' => $this->rincianTambahan(),
            'saran_harga' => $this->hitungHarga(),
        ];
    }
}

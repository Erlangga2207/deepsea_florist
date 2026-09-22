<?php

namespace App\Domain\Harga;

use App\Models\Pengaturan;
use App\Models\Produk;

// Pemanggil cukup: HargaFactory::untuk($produk)->hitungHarga(), tanpa perlu tahu kategorinya.
class HargaFactory
{
    // $opsi: jumlah_lembar, tarif_lipat (buket uang); sewa_rangka, ongkos_pasang (bunga papan)
    public static function untuk(Produk $produk, array $opsi = []): KalkulatorHarga
    {
        $pengaturan = Pengaturan::ambil();

        return match ($produk->kategori->tipe_harga) {
            'artificial' => new HargaArtificial($produk, $pengaturan),
            'fresh' => new HargaFresh($produk, $pengaturan),
            'uang' => new HargaUang($produk, $pengaturan, (float) ($opsi['jumlah_lembar'] ?? 0), (float) ($opsi['tarif_lipat'] ?? 0)),
            'papan' => new HargaPapan($produk, $pengaturan, (float) ($opsi['sewa_rangka'] ?? 0), (float) ($opsi['ongkos_pasang'] ?? 0)),
            default => new HargaUmum($produk, $pengaturan),
        };
    }
}

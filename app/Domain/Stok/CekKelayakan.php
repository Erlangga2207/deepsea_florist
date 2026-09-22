<?php

namespace App\Domain\Stok;

use App\Models\Produk;
use App\Models\StokProdukJadi;
use Illuminate\Support\Facades\Storage;

// "Model ini masih bisa dirakit dari stok yang ada atau tidak?"
class CekKelayakan
{
    public const MAKS_SARAN = 5;

    public function untukProduk(Produk $produk, int $qty = 1): HasilCek
    {
        $produk->loadMissing('komposisi.bahan');

        if ($produk->komposisi->isEmpty()) {
            return new HasilCek(layak: true, tanpaKomposisi: true);
        }

        $kurang = [];
        foreach ($produk->komposisi as $item) {
            $butuh = $item->jumlah * $qty;
            if ($butuh > $item->bahan->stok) {
                $kurang[] = [
                    'nama' => $item->bahan->nama,
                    'satuan' => $item->bahan->satuan,
                    'kurang' => $butuh - $item->bahan->stok,
                ];
            }
        }

        if (! $kurang) {
            return new HasilCek(layak: true);
        }

        return new HasilCek(layak: false, bahanKurang: $kurang, saranAlternatif: $this->saran($produk));
    }

    // Berapa buket yang masih bisa dirakit dari stok sekarang. Null bila tanpa komposisi.
    public function bisaDibuat(Produk $produk): ?int
    {
        $produk->loadMissing('komposisi.bahan');

        if ($produk->komposisi->isEmpty()) {
            return null;
        }

        return (int) $produk->komposisi
            ->map(fn ($item) => floor(max(0, $item->bahan->stok) / $item->jumlah))
            ->min();
    }

    // Badge katalog publik. Return [label, warna]
    public function badge(Produk $produk): array
    {
        return match (true) {
            $produk->status === 'preorder' => ['Pre-order', 'rose'],
            ! $this->untukProduk($produk)->layak => ['Bahan habis', 'merah'],
            default => ['Ready', 'hijau'],
        };
    }

    // Buket yang sudah jadi (paling cepat diserahkan), lalu model sekategori yang bahannya cukup.
    private function saran(Produk $produk): array
    {
        $saran = StokProdukJadi::where('status', 'tersedia')->with('produk')->latest()->get()
            ->map(fn ($s) => [
                'jenis' => 'stok_jadi',
                'produk_id' => $s->produk_id,
                'kode' => $s->produk?->kode,
                'nama' => $s->nama,
                'foto' => $this->urlFoto($s->foto),
                'keterangan' => '1 sudah jadi, siap ambil',
            ]);

        $sekategori = Produk::with('komposisi.bahan')
            ->where('kategori_id', $produk->kategori_id)
            ->where('is_aktif', true)
            ->whereKeyNot($produk->id)
            ->has('komposisi')
            ->get()
            ->map(fn ($p) => ['produk' => $p, 'bisa' => $this->bisaDibuat($p)])
            ->filter(fn ($x) => $x['bisa'] >= 1)
            ->sortByDesc('bisa')
            ->map(fn ($x) => [
                'jenis' => 'produk',
                'produk_id' => $x['produk']->id,
                'kode' => $x['produk']->kode,
                'nama' => $x['produk']->nama,
                'foto' => $this->urlFoto($x['produk']->foto_utama),
                'keterangan' => "Bahan cukup untuk {$x['bisa']} buket",
            ]);

        return $saran->concat($sekategori)->take(self::MAKS_SARAN)->values()->all();
    }

    private function urlFoto(?string $path): ?string
    {
        return $path ? Storage::url($path) : null;
    }
}

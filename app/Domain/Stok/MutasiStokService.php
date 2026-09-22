<?php

namespace App\Domain\Stok;

use App\Models\Bahan;
use App\Models\MutasiStok;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;

// Satu-satunya tempat yang boleh mengubah bahan.stok.
// $opsi: tanggal, tanggal_kadaluarsa, keterangan, pesanan_id, pengeluaran_id, user_id, harga_beli (khusus masuk)
class MutasiStokService
{
    public function masuk(Bahan $bahan, float $jumlah, array $opsi = []): MutasiStok
    {
        return DB::transaction(function () use ($bahan, $jumlah, $opsi) {
            $bahan->increment('stok', $jumlah);

            if (! empty($opsi['harga_beli'])) {
                $bahan->update(['harga_beli_terakhir' => $opsi['harga_beli']]);
            }

            return $this->catat($bahan, 'masuk', $jumlah, $opsi);
        });
    }

    // Hasil hitung fisik. Jumlah yang dicatat = selisihnya (selalu positif), arahnya ditulis di keterangan.
    public function penyesuaian(Bahan $bahan, float $stokBaru, string $alasan, array $opsi = []): MutasiStok
    {
        return DB::transaction(function () use ($bahan, $stokBaru, $alasan, $opsi) {
            $bahan->refresh();
            $stokLama = (float) $bahan->stok;
            $selisih = $stokBaru - $stokLama;

            $bahan->increment('stok', $selisih);

            $opsi['keterangan'] = sprintf(
                'Stok %s → %s (%s%s). %s',
                $this->angka($stokLama),
                $this->angka($stokBaru),
                $selisih >= 0 ? '+' : '−',
                $this->angka(abs($selisih)),
                $alasan
            );

            return $this->catat($bahan, 'penyesuaian', abs($selisih), $opsi);
        });
    }

    // Stok boleh jadi minus: peringatan tidak memblokir, yang memutuskan tetap manusia.
    public function keluar(Bahan $bahan, float $jumlah, array $opsi = []): MutasiStok
    {
        return DB::transaction(function () use ($bahan, $jumlah, $opsi) {
            $bahan->decrement('stok', $jumlah);

            return $this->catat($bahan, 'keluar', $jumlah, $opsi);
        });
    }

    // Dipanggil saat status → dikerjakan. Kalau pesanan ini sudah pernah mengambil bahan
    // (mis. status dimundurkan lalu dimajukan lagi), tidak diambil dua kali.
    public function keluarUntukPesanan(Pesanan $pesanan): array
    {
        if ($pesanan->mutasiStok()->where('tipe', 'keluar')->exists()) {
            return [];
        }

        return DB::transaction(function () use ($pesanan) {
            $hasil = [];
            foreach ($pesanan->item()->with('produk.komposisi.bahan')->get() as $item) {
                foreach ($item->produk?->komposisi ?? [] as $komposisi) {
                    $hasil[] = $this->keluar($komposisi->bahan, $komposisi->jumlah * $item->qty, [
                        'pesanan_id' => $pesanan->id,
                        'keterangan' => 'Dipakai untuk pesanan '.$pesanan->kode,
                    ]);
                }
            }

            return $hasil;
        });
    }

    private function angka(float $nilai): string
    {
        return rtrim(rtrim(number_format($nilai, 2, ',', '.'), '0'), ',');
    }

    private function catat(Bahan $bahan, string $tipe, float $jumlah, array $opsi): MutasiStok
    {
        return MutasiStok::create([
            'bahan_id' => $bahan->id,
            'tipe' => $tipe,
            'jumlah' => $jumlah,
            'tanggal' => $opsi['tanggal'] ?? now(),
            'tanggal_kadaluarsa' => $opsi['tanggal_kadaluarsa'] ?? null,
            'keterangan' => $opsi['keterangan'] ?? null,
            'pesanan_id' => $opsi['pesanan_id'] ?? null,
            'pengeluaran_id' => $opsi['pengeluaran_id'] ?? null,
            'user_id' => $opsi['user_id'] ?? auth()->id(),
        ]);
    }
}

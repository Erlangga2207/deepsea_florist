<?php

namespace App\Domain\Pesanan;

use App\Domain\Stok\MutasiStokService;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\StokProdukJadi;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

// Semua aturan bisnis pesanan ada di sini. Pesan DomainException langsung ditampilkan ke pengguna.
class PesananService
{
    public function __construct(private MutasiStokService $stok) {}

    public function buat(array $data, User $user): Pesanan
    {
        return DB::transaction(function () use ($data, $user) {
            $pesanan = Pesanan::create([
                'pelanggan_id' => $this->pelanggan($data)->id,
                'user_id' => $user->id,
                'tanggal_pesan' => today(),
                'tanggal_jadi' => $data['tanggal_jadi'],
                'metode_ambil' => $data['metode_ambil'],
                'alamat_antar' => $data['metode_ambil'] === 'antar' ? ($data['alamat_antar'] ?? null) : null,
                'biaya_tambahan' => $data['biaya_tambahan'] ?? 0,
                'ongkir' => $data['metode_ambil'] === 'antar' ? ($data['ongkir'] ?? 0) : 0,
                'catatan' => $data['catatan'] ?? null,
            ]);

            $produk = isset($data['produk_id']) ? Produk::find($data['produk_id']) : null;
            $pesanan->item()->create([
                'produk_id' => $produk?->id,
                'nama_item' => $produk?->nama ?? $data['nama_item'],
                'ukuran' => $data['ukuran'] ?? 'Normal',
                'warna' => $data['warna'] ?? null,
                'kartu_ucapan' => $data['kartu_ucapan'] ?? null,
                'qty' => $data['qty'],
                'harga' => $data['harga'],
                'subtotal' => $data['qty'] * $data['harga'],
            ]);

            $pesanan->hitungUlangTotal();

            if (! empty($data['dp_jumlah'])) {
                $this->bayar($pesanan, $data['dp_jumlah'], $data['dp_metode']);
            }

            return $pesanan;
        });
    }

    // Kontak boleh kosong. Kalau diisi dan sudah pernah pesan, pakai data pelanggan yang sama.
    private function pelanggan(array $data): Pelanggan
    {
        $atribut = ['nama' => $data['nama_pelanggan'], 'sumber' => $data['sumber']];

        return empty($data['kontak'])
            ? Pelanggan::create($atribut)
            : Pelanggan::firstOrCreate(['kontak' => $data['kontak']], $atribut);
    }

    public function ubahStatus(Pesanan $pesanan, string $ke, User $user, array $opsi = []): void
    {
        $dari = $pesanan->status;
        if ($dari === $ke) {
            return;
        }
        if (in_array($dari, ['batal', 'selesai']) && $user->role !== 'owner') {
            throw new DomainException("Pesanan yang sudah {$pesanan->labelStatus()} hanya bisa diubah pemilik.");
        }
        if ($dari === 'batal') {
            throw new DomainException('Pesanan batal tidak bisa diaktifkan lagi. Buat pesanan baru.');
        }

        if ($ke === 'batal') {
            $this->batalkan($pesanan, $opsi);

            return;
        }

        $posisiDari = array_search($dari, Pesanan::ALUR);
        $posisiKe = array_search($ke, Pesanan::ALUR);

        if ($posisiKe < $posisiDari && $user->role !== 'owner') {
            throw new DomainException('Memundurkan status hanya boleh dilakukan pemilik.');
        }
        if ($posisiKe > $posisiDari + 1) {
            throw new DomainException('Status harus berurutan: '.implode(' → ', Pesanan::ALUR).'. Tidak boleh melompat.');
        }
        if ($ke === 'selesai' && $pesanan->total_dibayar < $pesanan->total) {
            throw new DomainException('Belum bisa selesai: pelunasan Rp '.number_format($pesanan->sisaTagihan(), 0, ',', '.').' belum masuk.');
        }

        DB::transaction(function () use ($pesanan, $ke) {
            if ($ke === 'dikerjakan') {
                $this->stok->keluarUntukPesanan($pesanan);
            }
            $pesanan->update(['status' => $ke]);
        });
    }

    // DP tetap tercatat (hangus). Stok yang sudah keluar tidak dikembalikan.
    private function batalkan(Pesanan $pesanan, array $opsi): void
    {
        DB::transaction(function () use ($pesanan, $opsi) {
            $pesanan->update(['status' => 'batal']);

            if (! empty($opsi['masuk_stok_jadi'])) {
                $item = $pesanan->item()->with('produk')->first();
                StokProdukJadi::create([
                    'produk_id' => $item?->produk_id,
                    'pesanan_id' => $pesanan->id,
                    'nama' => $item?->nama_item ?? 'Buket pesanan '.$pesanan->kode,
                    'foto' => $item?->produk?->foto_utama,
                    'harga_jual' => $opsi['harga_jual'] ?? null,
                    'asal' => 'batal',
                ]);
            }
        });
    }

    // Maksimal dua kali bayar: DP lalu pelunasan. Pembayaran kedua wajib melunasi.
    public function bayar(Pesanan $pesanan, float $jumlah, string $metode, $tanggal = null): void
    {
        if ($pesanan->status === 'batal') {
            throw new DomainException('Pesanan batal tidak menerima pembayaran.');
        }
        $sisa = $pesanan->sisaTagihan();
        $sudah = $pesanan->pembayaran()->count();

        if ($sisa <= 0) {
            throw new DomainException('Pesanan ini sudah lunas.');
        }
        if ($sudah >= 2) {
            throw new DomainException('Sudah dua kali bayar. Maksimal dua kali pembayaran per pesanan.');
        }
        if ($jumlah > $sisa) {
            throw new DomainException('Jumlah melebihi sisa tagihan Rp '.number_format($sisa, 0, ',', '.').'.');
        }
        if ($sudah === 1 && $jumlah < $sisa) {
            throw new DomainException('Pembayaran kedua harus melunasi sisa Rp '.number_format($sisa, 0, ',', '.').'.');
        }

        DB::transaction(function () use ($pesanan, $jumlah, $metode, $tanggal, $sisa) {
            $pesanan->pembayaran()->create([
                'jenis' => $jumlah >= $sisa ? 'pelunasan' : 'dp',
                'jumlah' => $jumlah,
                'metode' => $metode,
                'tanggal' => $tanggal ?? today(),
            ]);
            $pesanan->update(['total_dibayar' => $pesanan->pembayaran()->sum('jumlah')]);
        });
    }
}

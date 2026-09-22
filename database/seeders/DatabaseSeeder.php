<?php

namespace Database\Seeders;

use App\Domain\Stok\MutasiStokService;
use App\Models\Bahan;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Pengeluaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\StokProdukJadi;
use App\Models\User;
use App\Support\FotoWebp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

// Data contoh dari docs/05-SEED-DATA.md, di atas ProduksiSeeder (pengaturan + kategori). Tanggal acuan demo: Sabtu, 19 September 2026.
class DatabaseSeeder extends Seeder
{
    private User $owner;

    public function run(): void
    {
        $this->owner = User::create([
            'name' => 'Siti Romlah',
            'email' => 'owner@deepseaflorist.test',
            'password' => 'password',
            'role' => 'owner',
        ]);

        User::create([
            'name' => 'Karyawan Deepsea',
            'email' => 'karyawan@deepseaflorist.test',
            'password' => 'password',
            'role' => 'karyawan',
        ]);

        $this->call(ProduksiSeeder::class);
        $this->produk();
        $this->bahanDanKomposisi();
        $pengeluaran = $this->pengeluaran();
        $this->stokAwal($pengeluaran);
        $this->pesanan();
        $this->stokProdukJadi();
    }

    private function produk(): void
    {
        // kode, nama, kategori, harga_dasar, estimasi_jam, kerumitan, status, foto
        $produk = [
            ['DF-001', 'Buket Lily Pink', 'buket-fresh', 185000, 2, 2, 'ready', 'p1'],
            ['DF-002', 'Buket Gerbera Oranye', 'buket-fresh', 145000, 1.5, 1, 'ready', 'p2'],
            ['DF-008', 'Buket Mawar Putih Medium', 'buket-fresh', 158000, 2, 2, 'ready', 'p4'],
            ['DF-014', 'Buket Mawar Biru Navy', 'buket-artificial', 225000, 2, 2, 'preorder', 'p3'],
            ['DF-016', 'Buket Krisan Putih', 'buket-fresh', 165000, 1.5, 1, 'ready', 'p4'],
            ['DF-021', 'Buket Tulip Pink', 'buket-artificial', 135000, 1.5, 1, 'ready', 'p5'],
            ['DF-030', 'Bunga Papan Ucapan', 'bunga-papan', 450000, 5, 3, 'preorder', 'p6'],
            ['DF-033', 'Buket Putih Wrap Hitam', 'buket-artificial', 155000, 2, 2, 'ready', 'p7'],
            ['DF-035', 'Buket Mini Wrap Hitam', 'buket-artificial', 95000, 1, 1, 'ready', 'p8'],
            ['DF-040', 'Buket Snack Cokelat', 'buket-snack', 175000, 2, 2, 'ready', 'p9'],
        ];

        foreach ($produk as [$kode, $nama, $kategoriSlug, $harga, $jam, $kerumitan, $status, $foto]) {
            Produk::create([
                'kode' => $kode,
                'nama' => $nama,
                'kategori_id' => Kategori::where('slug', $kategoriSlug)->value('id'),
                'harga_dasar' => $harga,
                'tampilkan_harga' => false,
                'estimasi_jam' => $jam,
                'faktor_kerumitan' => $kerumitan,
                'status' => $status,
                'foto_utama' => $this->fotoProduk($foto),
            ]);
        }
    }

    // Foto p1…p9 diambil dari storage/app/public/produk/ bila filenya sudah ditaruh di sana.
    private function fotoProduk(string $nama): ?string
    {
        $file = collect(File::glob(storage_path("app/public/produk/{$nama}.*")))
            ->first(fn ($f) => ! str_ends_with($f, '.webp'));
        if (! $file) {
            return null;
        }

        FotoWebp::buat($path = 'produk/'.basename($file));

        return $path;
    }

    private function bahanDanKomposisi(): void
    {
        // nama, jenis, satuan, minimum, harga_beli_terakhir, harga_eceran (null = tidak dijual eceran)
        $bahan = [
            ['Mawar putih', 'fresh', 'tangkai', 24, 4000, 7000],
            ['Mawar merah', 'fresh', 'tangkai', 24, 4000, 7000],
            ['Lily pink', 'fresh', 'tangkai', 12, 12000, 18000],
            ['Krisan putih', 'fresh', 'tangkai', 20, 3500, 6000],
            ["Baby's breath", 'fresh', 'ikat', 20, 15000, null],
            ['Gerbera oranye', 'fresh', 'tangkai', 15, 5000, 8000],
            ['Carnation', 'fresh', 'tangkai', 15, 4500, null],
            ['Eucalyptus', 'fresh', 'ikat', 8, 10000, null],
            ['Daun pakis', 'fresh', 'ikat', 8, 7000, null],
            ['Mawar artificial biru navy', 'artificial', 'tangkai', 40, 3000, null],
            ['Mawar artificial pink', 'artificial', 'tangkai', 40, 3000, null],
            ['Tulip artificial pink', 'artificial', 'tangkai', 40, 3500, null],
            ['Kertas wrapping', 'pendukung', 'lembar', 25, 2500, null],
            ['Kertas kraft cokelat', 'pendukung', 'lembar', 30, 2000, null],
            ['Plastik wrapping bening', 'pendukung', 'roll', 2, 25000, null],
            ['Pita satin 2,5 cm', 'pendukung', 'meter', 10, 3000, null],
            ['Tali rami', 'pendukung', 'meter', 10, 2000, null],
            ['Floral foam', 'pendukung', 'pcs', 6, 8000, null],
            ['Floral tape', 'pendukung', 'roll', 3, 12000, null],
            ['Kartu ucapan', 'pendukung', 'pcs', 25, 1500, null],
        ];

        foreach ($bahan as [$nama, $jenis, $satuan, $minimum, $hargaBeli, $eceran]) {
            Bahan::create([
                'nama' => $nama,
                'jenis' => $jenis,
                'satuan' => $satuan,
                'stok_minimum' => $minimum,
                'harga_beli_terakhir' => $hargaBeli,
                'dijual_eceran' => $eceran !== null,
                'harga_eceran' => $eceran,
            ]);
        }

        $komposisi = [
            'DF-008' => ['Mawar putih' => 12, "Baby's breath" => 1, 'Kertas wrapping' => 2, 'Pita satin 2,5 cm' => 1, 'Kartu ucapan' => 1],
            'DF-001' => ['Lily pink' => 5, 'Mawar putih' => 6, 'Kertas wrapping' => 2, 'Pita satin 2,5 cm' => 1],
            'DF-016' => ['Krisan putih' => 15, 'Eucalyptus' => 1, 'Kertas kraft cokelat' => 2, 'Tali rami' => 1],
            'DF-021' => ['Tulip artificial pink' => 12, 'Kertas wrapping' => 2, 'Pita satin 2,5 cm' => 1],
            'DF-014' => ['Mawar artificial biru navy' => 20, "Baby's breath" => 1, 'Kertas wrapping' => 3, 'Pita satin 2,5 cm' => 1.5],
            'DF-033' => ['Mawar artificial pink' => 10, 'Kertas wrapping' => 2, 'Pita satin 2,5 cm' => 1],
        ];

        foreach ($komposisi as $kode => $isi) {
            $produk = Produk::where('kode', $kode)->first();
            foreach ($isi as $namaBahan => $jumlah) {
                $produk->bahan()->attach(Bahan::where('nama', $namaBahan)->value('id'), ['jumlah' => $jumlah]);
            }
        }
    }

    private function pengeluaran(): array
    {
        $data = [
            'fresh' => ['2026-09-12', 'bahan', 850000, 'Kulakan bunga fresh Lembang'],
            'pendukung' => ['2026-09-05', 'bahan', 420000, 'Belanja kertas, pita, floral foam'],
            ['2026-09-01', 'sewa', 700000, 'Sewa kios September'],
            ['2026-09-01', 'gaji', 3600000, 'Gaji 3 karyawan September'],
            ['2026-09-08', 'listrik', 150000, 'Token listrik'],
            ['2026-09-03', 'wifi', 250000, 'Langganan Wi-Fi'],
        ];

        $hasil = [];
        foreach ($data as $kunci => [$tanggal, $kategori, $nominal, $keterangan]) {
            $hasil[$kunci] = Pengeluaran::create([
                'user_id' => $this->owner->id,
                'tanggal' => $tanggal,
                'kategori' => $kategori,
                'nominal' => $nominal,
                'keterangan' => $keterangan,
            ]);
        }

        return $hasil;
    }

    // Stok akhir di docs adalah stok SETELAH pesanan yang sudah dikerjakan mengambil bahannya.
    // Jadi jumlah masuk = stok akhir + yang nanti keluar untuk pesanan, supaya catatan mutasinya utuh.
    private function stokAwal(array $pengeluaran): void
    {
        $stokAkhir = [
            'Mawar putih' => 6, 'Mawar merah' => 36, 'Lily pink' => 18, 'Krisan putih' => 40,
            "Baby's breath" => 9, 'Gerbera oranye' => 24, 'Carnation' => 30, 'Eucalyptus' => 12,
            'Daun pakis' => 14, 'Mawar artificial biru navy' => 85, 'Mawar artificial pink' => 96,
            'Tulip artificial pink' => 120, 'Kertas wrapping' => 45, 'Kertas kraft cokelat' => 12,
            'Plastik wrapping bening' => 3, 'Pita satin 2,5 cm' => 18, 'Tali rami' => 22,
            'Floral foam' => 14, 'Floral tape' => 5, 'Kartu ucapan' => 60,
        ];

        $terpakai = [];
        foreach ($this->dataPesanan() as $p) {
            if ($p['tanggal_keluar'] === null) {
                continue;
            }
            foreach (Produk::where('kode', $p['produk'])->first()->komposisi as $item) {
                $terpakai[$item->bahan_id] = ($terpakai[$item->bahan_id] ?? 0) + $item->jumlah * ($p['lain']['qty'] ?? 1);
            }
        }

        $service = new MutasiStokService;

        foreach (Bahan::all() as $bahan) {
            $opsi = match ($bahan->jenis) {
                'fresh' => [
                    'tanggal' => '2026-09-12',
                    'tanggal_kadaluarsa' => '2026-09-26',
                    'pengeluaran_id' => $pengeluaran['fresh']->id,
                    'keterangan' => 'Kulakan Lembang',
                ],
                'pendukung' => [
                    'tanggal' => '2026-09-05',
                    'pengeluaran_id' => $pengeluaran['pendukung']->id,
                    'keterangan' => 'Belanja bahan pendukung',
                ],
                'artificial' => [
                    'tanggal' => '2026-08-28',
                    'keterangan' => 'Stok awal bunga artificial',
                ],
            };

            $jumlah = $stokAkhir[$bahan->nama] + ($terpakai[$bahan->id] ?? 0);
            $service->masuk($bahan, $jumlah, $opsi + ['user_id' => $this->owner->id]);
        }
    }

    // tanggal_pesan dan tanggal_keluar (hari status jadi "dikerjakan") tidak ada di docs, diisi perkiraan yang masuk akal.
    private function dataPesanan(): array
    {
        $p = fn (string $kode, string $nama, string $sumber, string $produk, string $pesan, string $jadi, string $status, string $bayar, ?string $keluar = null, array $lain = []) => compact('kode', 'nama', 'sumber', 'produk', 'status', 'bayar', 'lain') + [
            'tanggal_pesan' => $pesan,
            'tanggal_jadi' => $jadi,
            'tanggal_keluar' => $keluar,
        ];

        return [
            $p('DF-2609-038', 'Lilis Suryani', 'wa', 'DF-033', '2026-09-10', '2026-09-14', 'batal', 'dp', '2026-09-13'),
            $p('DF-2609-039', 'Iwan Setiawan', 'wa', 'DF-016', '2026-09-14', '2026-09-24', 'dikerjakan', 'lunas', '2026-09-19'),
            // 040 & 044 tambahan (tidak ada di tabel awal docs) supaya 22 Sep kelebihan beban: 2+2+10+5 = 19 jam > 18
            $p('DF-2609-040', 'Ujang Suherman', 'wa', 'DF-030', '2026-09-14', '2026-09-22', 'masuk', 'dp', null, [
                'catatan' => 'Dua papan untuk pernikahan di Kalijati',
                'metode_ambil' => 'antar',
                'qty' => 2,
            ]),
            $p('DF-2609-041', 'Neneng Sulastri', 'wa', 'DF-014', '2026-09-15', '2026-09-19', 'dikerjakan', 'dp', '2026-09-18', [
                'catatan' => 'Wisuda Polsub',
                'kartu_ucapan' => 'Selamat wisuda, Teh. Bangga pisan.',
            ]),
            $p('DF-2609-042', 'Dedi Supriatna', 'ig', 'DF-001', '2026-09-15', '2026-09-20', 'dikerjakan', 'lunas', '2026-09-19', [
                'catatan' => 'Ulang tahun istri',
            ]),
            $p('DF-2609-043', 'Rina Marlina', 'wa', 'DF-040', '2026-09-16', '2026-09-23', 'jadi', 'dp', '2026-09-19', [
                'metode_ambil' => 'antar',
                'alamat_antar' => 'Pagaden',
                'ongkir' => 20000,
            ]),
            $p('DF-2609-044', 'Euis Komariah', 'ig', 'DF-030', '2026-09-16', '2026-09-22', 'masuk', 'dp', null, [
                'catatan' => 'Syukuran kantor desa',
                'metode_ambil' => 'antar',
            ]),
            $p('DF-2609-045', 'Asep Kurniawan', 'tiktok', 'DF-030', '2026-09-17', '2026-09-19', 'masuk', 'dp', null, [
                'catatan' => 'Pembukaan toko',
                'metode_ambil' => 'antar',
            ]),
            $p('DF-2609-046', 'Yuyun Yuningsih', 'wa', 'DF-021', '2026-09-17', '2026-09-21', 'masuk', 'dp'),
            $p('DF-2609-047', 'Hendra Gunawan', 'ig', 'DF-008', '2026-09-18', '2026-09-22', 'masuk', 'dp'),
            $p('DF-2609-048', 'Wulan Purnamasari', 'wa', 'DF-040', '2026-09-19', '2026-09-22', 'masuk', 'dp'),
        ];
    }

    private function pesanan(): void
    {
        $service = new MutasiStokService;

        foreach ($this->dataPesanan() as $d) {
            $produk = Produk::where('kode', $d['produk'])->first();
            $lain = $d['lain'];
            $ongkir = $lain['ongkir'] ?? 0;
            $qty = $lain['qty'] ?? 1;
            $subtotal = $produk->harga_dasar * $qty;
            $total = $subtotal + $ongkir;

            $pelanggan = Pelanggan::create(['nama' => $d['nama'], 'sumber' => $d['sumber']]);

            $pesanan = Pesanan::create([
                'kode' => $d['kode'],
                'pelanggan_id' => $pelanggan->id,
                'user_id' => $this->owner->id,
                'tanggal_pesan' => $d['tanggal_pesan'],
                'tanggal_jadi' => $d['tanggal_jadi'],
                'metode_ambil' => $lain['metode_ambil'] ?? 'ambil',
                'alamat_antar' => $lain['alamat_antar'] ?? null,
                'subtotal' => $subtotal,
                'ongkir' => $ongkir,
                'total' => $total,
                'status' => $d['status'],
                'catatan' => $lain['catatan'] ?? null,
            ]);

            $pesanan->item()->create([
                'produk_id' => $produk->id,
                'nama_item' => $produk->nama,
                'kartu_ucapan' => $lain['kartu_ucapan'] ?? null,
                'qty' => $qty,
                'harga' => $produk->harga_dasar,
                'subtotal' => $subtotal,
            ]);

            // DP 50% di tanggal pesan; yang lunas dilunasi di hari yang sama.
            $dp = $total / 2;
            $pesanan->pembayaran()->create(['jenis' => 'dp', 'jumlah' => $dp, 'metode' => 'transfer', 'tanggal' => $d['tanggal_pesan']]);
            if ($d['bayar'] === 'lunas') {
                $pesanan->pembayaran()->create(['jenis' => 'pelunasan', 'jumlah' => $total - $dp, 'metode' => 'transfer', 'tanggal' => $d['tanggal_pesan']]);
            }
            $pesanan->update(['total_dibayar' => $pesanan->pembayaran()->sum('jumlah')]);

            if ($d['tanggal_keluar']) {
                foreach ($produk->komposisi as $item) {
                    $service->keluar($item->bahan, $item->jumlah * $qty, [
                        'tanggal' => $d['tanggal_keluar'],
                        'pesanan_id' => $pesanan->id,
                        'user_id' => $this->owner->id,
                        'keterangan' => 'Dipakai untuk pesanan '.$pesanan->kode,
                    ]);
                }
            }
        }
    }

    private function stokProdukJadi(): void
    {
        $batal = Produk::where('kode', 'DF-033')->first();
        StokProdukJadi::create([
            'produk_id' => $batal->id,
            'pesanan_id' => Pesanan::where('kode', 'DF-2609-038')->value('id'),
            'nama' => $batal->nama,
            'foto' => $batal->foto_utama,
            'asal' => 'batal',
            'created_at' => Carbon::parse('2026-09-14'),
        ]);

        $gerbera = Produk::where('kode', 'DF-002')->first();
        StokProdukJadi::create([
            'produk_id' => $gerbera->id,
            'nama' => $gerbera->nama,
            'foto' => $gerbera->foto_utama,
            'asal' => 'produksi',
            'created_at' => Carbon::parse('2026-09-17'),
        ]);
    }
}

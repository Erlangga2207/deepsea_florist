# Arsitektur & Rancangan Kelas

## 1. Struktur folder

```
app/
├─ Http/
│  ├─ Controllers/
│  │  ├─ Publik/        BerandaController, KatalogController
│  │  └─ Admin/         DashboardController, PesananController, ProdukController,
│  │                    KategoriController, BahanController, MutasiStokController,
│  │                    PengeluaranController, LaporanController,
│  │                    StokProdukJadiController, PenggunaController, PengaturanController
│  ├─ Requests/         PesananStoreRequest, ProdukStoreRequest, BahanStoreRequest, ...
│  └─ Middleware/       RoleOwner
├─ Models/              User, Kategori, Produk, ProdukFoto, ProdukBahan, Bahan,
│                       Pelanggan, Pesanan, PesananItem, Pembayaran, MutasiStok,
│                       Pengeluaran, StokProdukJadi, Pengaturan
├─ Domain/
│  ├─ Harga/            KalkulatorHarga (abstract), HargaArtificial, HargaFresh,
│  │                    HargaUang, HargaPapan, HargaUmum, HargaFactory
│  ├─ Stok/             CekKelayakan, HasilCek, MutasiStokService
│  └─ Jadwal/           CekKapasitas, HasilKapasitas
resources/views/
├─ publik/              beranda, katalog, detail
├─ admin/               dashboard, pesanan/, produk/, bahan/, pengeluaran/, laporan/
├─ components/          pill.blade.php, tile.blade.php, panel.blade.php, ...
└─ layouts/             publik.blade.php, admin.blade.php
```

**Controller tipis.** Semua logika hitungan ada di `app/Domain`. Controller hanya: ambil request → panggil service → kirim ke view.

---

## 2. Kelas harga — inti OOP mata kuliah

Ini bagian yang memperlihatkan pilar OOP dipakai karena dibutuhkan, bukan karena disuruh.

### 2.1 Kelas induk abstract

`app/Domain/Harga/KalkulatorHarga.php`

```php
abstract class KalkulatorHarga
{
    public function __construct(
        protected Produk $produk,
        protected Pengaturan $pengaturan
    ) {}

    // sama untuk semua kategori
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

    // wajib ditulis ulang tiap turunan
    abstract public function hitungHarga(): float;
    abstract public function labelKategori(): string;

    public function rincian(): array
    {
        return [
            'modal_bahan' => $this->modalBahan(),
            'ongkos_jasa' => $this->ongkosJasa(),
            'saran_harga' => $this->hitungHarga(),
        ];
    }
}
```

### 2.2 Turunan dan rumusnya

| Kelas | `hitungHarga()` | Kenapa beda |
|---|---|---|
| `HargaArtificial` | `(modalBahan × margin) + ongkosJasa` | Rumus dasar |
| `HargaFresh` | `(modalBahan × margin × 1.10) + ongkosJasa` | Tambahan 10% cadangan risiko bunga layu |
| `HargaUang` | `(jumlahLembar × tarifLipat) + ongkosJasa` | Uangnya milik pelanggan, bukan modal toko |
| `HargaPapan` | `sewaRangka + modalBahan + ongkosPasang + ongkosJasa` | Ada sewa rangka dan ongkos pasang di lokasi |
| `HargaUmum` | `(modalBahan × margin) + ongkosJasa` | Cadangan untuk kategori baru (dekorasi) |

`HargaUang` dan `HargaPapan` butuh angka tambahan (`jumlahLembar`, `tarifLipat`, `sewaRangka`, `ongkosPasang`). Ambil dari input form di halaman pesanan, dilewatkan ke constructor sebagai parameter opsional. **Jangan** menambah kolom baru ke tabel produk untuk ini.

### 2.3 Factory

`HargaFactory::untuk(Produk $produk, array $opsi = []): KalkulatorHarga`

Memilih kelas berdasarkan `produk.kategori.tipe_harga`:

```
artificial → HargaArtificial
fresh      → HargaFresh
uang       → HargaUang
papan      → HargaPapan
umum       → HargaUmum
```

Pemanggilnya cukup menulis:

```php
$kalkulator = HargaFactory::untuk($produk);
$saran = $kalkulator->hitungHarga();
```

tanpa perlu tahu kategorinya apa. **Inilah polymorphism-nya.**

### 2.4 Pemetaan ke pilar OOP (untuk laporan & presentasi)

| Pilar | Wujudnya di sistem |
|---|---|
| Abstraction | `KalkulatorHarga` menetapkan bahwa setiap produk pasti bisa dihitung harganya, tanpa menentukan caranya |
| Inheritance | Empat turunan mewarisi `modalBahan()` dan `ongkosJasa()` — tidak ada kode yang ditulis ulang |
| Overriding / Polymorphism | `hitungHarga()` ditulis ulang di tiap turunan; pemanggil tidak peduli kategorinya |
| Encapsulation | `bahan.stok` hanya bisa berubah lewat `MutasiStokService`, bukan diubah langsung dari luar |

---

## 3. Service cek kelayakan

`app/Domain/Stok/CekKelayakan.php`

```php
public function untukProduk(Produk $produk, int $qty = 1): HasilCek
```

Langkah:
1. Kalau `$produk->komposisi` kosong → kembalikan `HasilCek` dengan `layak = true` dan `tanpaKomposisi = true`
2. Untuk tiap komposisi: `butuh = jumlah × qty`, bandingkan dengan `bahan.stok`
3. Kumpulkan yang kurang ke `bahanKurang[]` berisi `['nama', 'satuan', 'kurang']`
4. Kalau ada yang kurang, isi `saranAlternatif[]`:
   - produk lain dengan `kategori_id` sama, `is_aktif = true`, yang semua komposisinya cukup — hitung juga `bisaDibuat` (berapa buket masih mungkin)
   - ditambah `stok_produk_jadi` berstatus `tersedia`
   - maksimal 5 item

`HasilCek` adalah class biasa (plain PHP object) dengan properti public: `layak`, `tanpaKomposisi`, `bahanKurang`, `saranAlternatif`, dan method `pesan(): string`.

---

## 4. Service kapasitas

`app/Domain/Jadwal/CekKapasitas.php`

```php
public function untukTanggal(Carbon $tanggal, float $tambahanJam = 0): HasilKapasitas
```

```
beban     = Pesanan::whereIn('status', ['masuk','dikerjakan','jadi'])
                   ->whereDate('tanggal_jadi', $tanggal)
                   ->sum(estimasi jam seluruh itemnya)
kapasitas = pengaturan.jumlah_perakit × pengaturan.jam_kerja_per_hari
penuh     = (beban + tambahanJam) > kapasitas
```

Kalau penuh, cari tanggal terdekat (maksimal 7 hari ke depan) yang masih muat, kembalikan sebagai `tanggalSaran`.

Dipakai juga oleh dashboard untuk menggambar batang beban 7 hari.

---

## 5. Service mutasi stok

`app/Domain/Stok/MutasiStokService.php` — **satu-satunya** tempat yang boleh mengubah `bahan.stok`.

```php
public function masuk(Bahan $bahan, float $jumlah, array $opsi = []): MutasiStok
public function keluar(Bahan $bahan, float $jumlah, array $opsi = []): MutasiStok
public function penyesuaian(Bahan $bahan, float $stokBaru, string $alasan): MutasiStok
public function keluarUntukPesanan(Pesanan $pesanan): array   // dipanggil saat status → dikerjakan
```

Semua dibungkus `DB::transaction`.

---

## 6. Alur perubahan status pesanan

Ditangani `PesananController@ubahStatus`:

| Dari → Ke | Yang terjadi |
|---|---|
| masuk → dikerjakan | `MutasiStokService::keluarUntukPesanan()` mengurangi stok sesuai komposisi tiap item |
| dikerjakan → jadi | Tidak ada efek data |
| jadi → selesai | **Ditolak** bila `total_dibayar < total`. Kalau lolos, status berubah |
| apa pun → batal | Stok yang sudah keluar **tidak** dikembalikan (bahan sudah terpakai). Muncul pilihan "masukkan buket ke Stok Produk Jadi" |
| mundur (mis. jadi → dikerjakan) | Hanya role `owner` |

## 7. Hak akses

Middleware `RoleOwner` dipasang pada group route: `laporan`, `pengeluaran`, `pengguna`, `pengaturan`.
Di Blade, sembunyikan menunya dengan `@if(auth()->user()->role === 'owner')`.

## 8. Route (ringkasan)

```
GET  /                         publik.beranda
GET  /katalog                  publik.katalog
GET  /katalog/{produk:slug}    publik.detail
GET  /tentang                  publik.tentang
GET  /kontak                   publik.kontak
GET  /faq                      publik.faq

GET  /admin                    admin.dashboard
resource /admin/pesanan
POST /admin/pesanan/{pesanan}/status
POST /admin/pesanan/{pesanan}/pembayaran
POST /admin/pesanan/cek-kelayakan      (AJAX, dipanggil form pesanan baru)
resource /admin/produk
resource /admin/kategori
resource /admin/bahan
resource /admin/mutasi-stok  (hanya index, create, store)
resource /admin/pengeluaran
resource /admin/stok-produk-jadi
GET  /admin/laporan
GET  /admin/laporan/cetak              (dompdf)
GET  /admin/daftar-belanja
resource /admin/pengguna
resource /admin/faq            (owner, tanpa show)
GET/PUT /admin/pengaturan
```

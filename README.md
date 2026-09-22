# Deepsea Florist — Sistem Informasi Toko Bunga

Sistem informasi untuk **Deepsea Florist Home Store**, toko bunga di Cibogo, Subang. Dibuat sebagai project mata kuliah Pemrograman Berorientasi Objek 1, D-IV Teknologi Rekayasa Perangkat Lunak, Politeknik Negeri Subang.

Aplikasi ini punya dua bagian dalam satu project Laravel:

- **Halaman publik** — company profile dan katalog produk yang bisa dikelola sendiri oleh pemilik
- **Panel admin** — pencatatan pesanan, stok bahan, pengeluaran, dan laporan

## Kenapa bukan aplikasi penjualan biasa

Buket **dirakit**, bukan diambil dari rak. Yang dikelola sistem ini bukan stok barang jadi, melainkan stok bahan beserta komposisi tiap model. Perumpamaannya lebih dekat ke dapur restoran daripada ke rak minimarket.

Dari situ lahir tiga fitur yang tidak ada di sistem penjualan umum:

1. **Cek kelayakan pesanan** — sistem menghitung apakah sebuah model masih bisa dirakit dari stok yang ada, dan menyarankan model alternatif bila bahannya kurang
2. **Kalkulator harga** — dihitung dari modal bahan dan tingkat kerumitan, dengan rumus berbeda tiap kategori produk
3. **Peringatan kapasitas** — memperingatkan bila beban pengerjaan pada satu tanggal sudah melebihi jam kerja para perakit

## Stack

PHP 8.2 · Laravel 12 · MySQL 8 · Blade · Bootstrap 5 · dompdf

## Menjalankan

```bash
composer install
cp .env.example .env
php artisan key:generate
# atur DB_DATABASE=deepsea_florist di .env
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Tidak perlu `npm`: Bootstrap dan Google Fonts dimuat lewat CDN, CSS sendiri ada di `public/css/app.css`.

Akun contoh setelah seeding:

| Peran | Email | Password |
|---|---|---|
| Owner | owner@deepseaflorist.test | password |
| Karyawan | karyawan@deepseaflorist.test | password |

### Mode demo (tanggal acuan 19 September 2026)

Data contoh dan ceklis di `docs/07-ACCEPTANCE.md` memakai tanggal acuan **Sabtu, 19 September 2026**. Supaya "Hari ini", antrian, dan batang beban 7 hari cocok dengan ceklis, isi di `.env`:

```
TANGGAL_DEMO=2026-09-19
```

Hanya berlaku bila `APP_ENV=local`. Kosongkan untuk memakai tanggal sungguhan.

### Foto produk

Taruh `p1.jpg` … `p9.jpg` (dari zip `Foto_untuk_Figma_Deepsea`) di `storage/app/public/produk/`, lalu jalankan `php artisan migrate:fresh --seed`. Seeder memasang foto ke produk dan membuat versi WebP otomatis.

## Deploy ke hosting

1. Unggah project, arahkan **document root ke folder `public`**.
2. Buat database MySQL, lalu salin `.env.example` → `.env` dan isi:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-toko.com
   TANGGAL_DEMO=
   DB_DATABASE=… DB_USERNAME=… DB_PASSWORD=…
   ```
3. Jalankan:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --class=ProduksiSeeder --force   # pengaturan toko + 6 kategori, TANPA data contoh
   php artisan akun:owner                              # buat akun asli Bu Siti (sandi diketik sendiri)
   php artisan storage:link
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```
4. Masuk sebagai pemilik → **Pengaturan**: cek nomor WA, isi link Instagram/TikTok, koordinat & link Google Maps.
5. Buat akun karyawan dari menu **Pengguna**. Isi bahan, produk, dan komposisinya dari panel.
6. Setelah domain aktif, uji `/robots.txt`, `/sitemap.xml`, dan Rich Results Test Google untuk beranda.

Jangan menjalankan `migrate:fresh --seed` di hosting — itu menghapus semua data dan mengisi data contoh.

## Dokumentasi

| File | Isi |
|---|---|
| `CLAUDE.md` | Panduan kerja, aturan bisnis, batasan — **baca pertama** |
| `docs/01-PRD.md` | Masalah mitra, pengguna, ruang lingkup |
| `docs/02-DATABASE.md` | 14 tabel dan relasinya |
| `docs/03-ARCHITECTURE.md` | Struktur folder dan rancangan kelas OOP |
| `docs/04-UI-GUIDE.md` | Warna, huruf, komponen, 8 layar |
| `docs/05-SEED-DATA.md` | Data contoh untuk seeder |
| `docs/06-BUILD-PLAN.md` | Urutan pengerjaan per tahap |
| `docs/07-ACCEPTANCE.md` | Ceklis "selesai" tiap fitur |
| `docs/09-RINGKASAN-PENGERJAAN.md` | Ringkasan semua yang sudah dikerjakan, keputusan, dan sisa pekerjaan |

Rancangan tampilan ada di Figma: **Deepsea Florist — Mockup UI**.

## Catatan

Seluruh data di seeder adalah **data contoh**, bukan data asli mitra. Ganti setelah data asli dari pemilik masuk.

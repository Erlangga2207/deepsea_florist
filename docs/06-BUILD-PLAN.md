# Rencana Pengerjaan

Kerjakan **satu tahap per sesi**. Setelah tiap tahap: jalankan `php artisan migrate:fresh --seed`, buka halamannya di browser, pastikan tidak error, baru lanjut ke tahap berikutnya.

Kolom "Pertemuan" mengacu ke silabus PBO 1 supaya progres kelompok sejalan dengan kuliah.

---

## Tahap 0 — Fondasi project
**Pertemuan 9**

- [x] `laravel new` (Laravel 12), atur `.env` ke MySQL `deepsea_florist`
- [x] Install Breeze versi **Blade**, lalu **hapus route `register`** di `routes/auth.php` dan tombol daftar di view login
- [x] Install `barryvdh/laravel-dompdf`
- [x] Buat layout `layouts/publik.blade.php` dan `layouts/admin.blade.php`
- [x] Pasang Bootstrap 5 + file CSS token dari `docs/04-UI-GUIDE.md` + Google Fonts (Fraunces, Source Sans 3)
- [x] Buat middleware `RoleOwner` dan daftarkan

**Selesai bila:** bisa login, halaman admin kosong tampil dengan warna dan huruf yang benar.

---

## Tahap 1 — Database & seeder
**Pertemuan 9**

- [x] 14 migrasi sesuai `docs/02-DATABASE.md`, urut sesuai daftar
- [x] 14 Model + relasi Eloquent lengkap (`hasMany`, `belongsTo`, `belongsToMany` untuk komposisi)
- [x] `pesanan.kode` dibuat otomatis di `Pesanan::booted()`
- [x] Seeder lengkap sesuai `docs/05-SEED-DATA.md`, termasuk mutasi stok awal
- [x] `php artisan migrate:fresh --seed` jalan tanpa error

**Selesai bila:** tabel `bahan` menunjukkan mawar putih sisa 6 (di bawah minimum 24), dan ada 11 pesanan di tabel `pesanan`.

---

## Tahap 2 — CRUD katalog
**Pertemuan 10–11**

- [x] CRUD Kategori
- [x] CRUD Produk + upload foto utama + galeri foto
- [x] Form komposisi bahan di halaman edit produk (tambah/hapus baris bahan + jumlah)
- [x] CRUD Bahan
- [x] Halaman Mutasi Stok: catat masuk (belanja), keluar manual, penyesuaian — lewat `MutasiStokService`
- [x] Manajemen Pengguna (khusus owner)
- [x] Halaman Pengaturan (khusus owner)

**Selesai bila:** owner bisa menambah produk baru lengkap dengan komposisinya tanpa menyentuh database.

---

## Tahap 3 — Kelas harga (inti OOP)
**Pertemuan 11**

- [x] `app/Domain/Harga/KalkulatorHarga.php` (abstract)
- [x] Empat turunan + `HargaUmum`
- [x] `HargaFactory`
- [x] Halaman uji sederhana: buka detail produk di admin, tampilkan rincian harga (modal bahan, ongkos jasa, saran harga)

**Selesai bila:** dua produk dari kategori berbeda menghasilkan angka berbeda dari rumus yang berbeda, dan bisa dijelaskan ke dosen.

---

## Tahap 4 — Pesanan & pembayaran
**Pertemuan 12**

- [x] Daftar pesanan + tab status + urut `tanggal_jadi` terdekat
- [x] Form pesanan baru (kolom kiri saja dulu) + simpan + item pesanan
- [x] Detail pesanan
- [x] Ubah status dengan aturan di `docs/03-ARCHITECTURE.md` bagian 6
- [x] Input pembayaran DP & pelunasan, perbarui `total_dibayar`
- [x] Blokir status `selesai` bila belum lunas
- [x] Saat status → `dikerjakan`, stok bahan berkurang lewat `MutasiStokService`
- [x] Saat status → `batal`, tawarkan masuk ke Stok Produk Jadi

**Selesai bila:** satu pesanan bisa berjalan dari masuk sampai selesai, dan stok bahan ikut berubah.

---

## Tahap 5 — Tiga fitur andalan
**Pertemuan 12–13**

- [x] `CekKelayakan` + `HasilCek`
- [x] Kolom kanan form pesanan: peringatan bahan kurang + saran model alternatif + tombol Pakai
- [x] Kotak perkiraan harga dengan rincian hitungannya
- [x] `CekKapasitas` + peringatan tanggal padat + saran tanggal
- [x] Badge Ready / Pre-order / Bahan habis di katalog publik, dihitung dari komposisi

**Selesai bila:** memilih DF-008 (Buket Mawar Putih Medium) memunculkan "Kurang 6 tangkai mawar putih" beserta tiga saran alternatif, dan memilih tanggal 22 September memunculkan peringatan kapasitas.

---

## Tahap 6 — Halaman publik
**Pertemuan 13**

- [x] Beranda
- [x] Katalog + filter kategori
- [x] Detail produk + tombol WhatsApp dengan pesan otomatis
- [x] Data toko (alamat, jam buka, sosmed) diambil dari tabel `pengaturan`

**Selesai bila:** klik tombol WhatsApp di detail produk membuka chat dengan pesan berisi nama dan kode model.

---

## Tahap 7 — Dashboard, pengeluaran, laporan
**Pertemuan 13–14**

- [x] Dashboard: 4 tile, antrian hari ini, beban 7 hari, bahan menipis, siap dijual
- [x] CRUD Pengeluaran
- [x] Halaman Daftar Belanja: bahan di bawah minimum + kebutuhan pesanan yang belum dikerjakan
- [x] Laporan penjualan, pengeluaran, laba sederhana, dengan filter rentang tanggal
- [x] Cetak laporan ke PDF lewat dompdf

**Selesai bila:** laporan bulan September 2026 bisa dicetak jadi PDF dan angkanya cocok dengan data seeder.

---

## Tahap 8 — Rapikan & hosting
**Pertemuan 14–15**

- [x] Cek seluruh halaman admin di lebar 400px
- [x] Kompres foto produk ke WebP
- [x] Halaman error 403/404 sederhana
- [ ] Deploy ke hosting, arahkan document root ke folder `public` — langkahnya di README bagian "Deploy ke hosting"
- [ ] Buat akun asli untuk Bu Siti, ganti data contoh dengan data asli — `php artisan db:seed --class=ProduksiSeeder` lalu `php artisan akun:owner`
- [ ] Sosialisasi ke mitra

---

## Kalau waktunya mepet

Urutan yang **boleh** dipotong, dari yang paling aman dibuang:

1. Galeri foto tambahan per produk (cukup foto utama)
2. Halaman Daftar Belanja (bisa dilihat manual dari Bahan & stok)
3. Stok Produk Jadi (tabel tetap dibuat, halamannya belakangan)
4. Grafik di halaman laporan (tabel saja cukup)

Yang **tidak boleh** dipotong: tiga fitur andalan di Tahap 5 dan kelas harga di Tahap 3. Itu yang membedakan project ini dari CRUD biasa.

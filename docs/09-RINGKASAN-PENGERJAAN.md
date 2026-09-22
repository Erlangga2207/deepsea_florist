# Ringkasan Pengerjaan

Catatan semua yang sudah dikerjakan pada Tahap 0–8 (`docs/06-BUILD-PLAN.md`), per **23 September 2026**. Dokumen ini untuk anggota kelompok yang belum ikut mengerjakan, dan sebagai bahan laporan/presentasi ke dosen.

---

## 1. Status singkat

| Tahap | Isi | Status | Uji otomatis |
|---|---|---|---|
| 0 | Fondasi: Laravel 12, Breeze (login saja), dompdf, layout, token warna, middleware `RoleOwner` | Selesai | login, 403, register 404 |
| 1 | 14 tabel, 14 model + relasi, seeder data contoh | Selesai | stok & 11 pesanan cocok dengan docs |
| 2 | CRUD kategori, produk (+foto, komposisi), bahan, mutasi stok, pengguna, pengaturan | Selesai | 25 lulus |
| 3 | Kelas harga OOP (`KalkulatorHarga` + 5 turunan + factory) | Selesai | 8 lulus |
| 4 | Pesanan, status, pembayaran DP & pelunasan, stok berkurang otomatis | Selesai | 37 lulus |
| 5 | Tiga fitur andalan: cek kelayakan, perkiraan harga, peringatan kapasitas | Selesai | 26 lulus + 13 uji JavaScript |
| 6 | Halaman publik + SEO | Selesai | 39 lulus |
| 7 | Dashboard, pengeluaran, daftar belanja, stok produk jadi, laporan + PDF | Selesai | 62 lulus |
| 8 | Cek HP 400px, WebP, halaman error, persiapan hosting | Bagian kode selesai | 28 lulus |

**Yang belum:** deploy ke hosting, akun asli Bu Siti, sosialisasi ke mitra, foto produk asli (p1–p9). Semua pekerjaan belum di-commit ke git.

---

## 2. Cara menjalankan

```bash
composer install
cp .env.example .env
php artisan key:generate
# .env: DB_DATABASE=deepsea_florist, TANGGAL_DEMO=2026-09-19
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Tidak perlu `npm`. Akun contoh: `owner@deepseaflorist.test` dan `karyawan@deepseaflorist.test`, kata sandi `password`.

**Mode demo.** Semua ceklis di `docs/07-ACCEPTANCE.md` memakai tanggal acuan Sabtu 19 September 2026. Dengan `TANGGAL_DEMO=2026-09-19` di `.env`, aplikasi menganggap hari ini tanggal itu, jadi "Hari ini", antrian, dan batang beban 7 hari cocok dengan ceklis. Hanya aktif bila `APP_ENV=local`; kosongkan di hosting.

Langkah deploy ada di `README.md` bagian **Deploy ke hosting**.

---

## 3. Stack & keputusan teknis

| Hal | Keputusan | Alasan |
|---|---|---|
| CSS | Bootstrap 5 + Google Fonts lewat **CDN**, CSS sendiri di `public/css/app.css` | Breeze membawa Tailwind yang dilarang CLAUDE.md. CDN juga menghilangkan langkah `npm run build` untuk pemula |
| Autentikasi | Breeze dipangkas: **hanya login & logout** | Register dilarang. Lupa sandi & verifikasi email butuh kirim email (dilarang). Halaman profil punya tombol "hapus akun" yang berbahaya. Reset sandi dilakukan owner lewat menu Pengguna |
| Warna | Semua dari token CSS di `:root`, tidak ada hex di Blade | Aturan I1. Pengecualian: `laporan/pdf.blade.php`, karena dompdf tidak membaca CSS variable |
| Zona waktu & bahasa | `Asia/Jakarta`, `id` | Tanggal dan pesan validasi berbahasa Indonesia |
| Format angka | Directive Blade `@rupiah(...)` dan `@angka(...)` di `AppServiceProvider` | `@angka(36.00)` → `36`, `@angka(1.5)` → `1,5` |
| Menu sidebar | Satu array di `layouts/admin.blade.php`, item muncul otomatis bila route-nya ada | Menu khusus owner disembunyikan untuk karyawan |

---

## 4. Yang dibuat per tahap

### Tahap 0 — Fondasi
- Laravel 12 + MySQL `deepsea_florist`, Breeze Blade, `barryvdh/laravel-dompdf`.
- `layouts/publik.blade.php`, `layouts/admin.blade.php`, `layouts/aset.blade.php` (font + CSS).
- Middleware `RoleOwner` (alias `owner`) → 403 bila bukan owner.
- Login menolak akun dengan `is_aktif = false`.

### Tahap 1 — Database & seeder
- 13 migrasi baru ditambah tabel `users` = 14 tabel, sesuai `docs/02-DATABASE.md`.
- Kolom SEO dari `docs/08-SEO.md` (A1) langsung dimasukkan ke `pengaturan`, `produk`, dan `kategori`.
- 14 model dengan relasi lengkap. Komposisi bisa dibaca dua cara: `$produk->komposisi` (hasMany) dan `$produk->bahan` (belongsToMany, jumlah di `pivot->jumlah`).
- Kode otomatis: pesanan `DF-YYMM-NNN` (nomor urut mulai lagi tiap bulan), produk `DF-NNN`.
- **Stok awal lahir dari mutasi**, bukan diisi langsung: jumlah masuk = stok akhir di docs + bahan yang sudah terpakai pesanan. Hasilnya, untuk setiap bahan stok = total masuk − total keluar.

### Tahap 2 — CRUD katalog
- **Kategori** — tipe rumus harga dipilih dari daftar. Tidak bisa dihapus bila masih dipakai produk.
- **Produk** — foto utama + galeri. Nama file dibuat dari slug, mis. `buket-lily-pink-deepsea-florist-x7k2p.jpg`. Editor komposisi bisa tambah/hapus baris, dan bahan yang sama tidak boleh dipilih dua kali.
- **Bahan & stok** — tab per jenis, kolom sisa/minimum/status/masuk terakhir/catatan umur. Stok tidak bisa diubah dari form bahan. Bahan yang masih dipakai komposisi tidak bisa dihapus.
- **Mutasi stok** — masuk (belanja, bisa sekaligus memperbarui harga beli dan tanggal layu), keluar, dan penyesuaian (wajib ada alasan).
- **Pengguna** (owner) — tambah/ubah akun dan reset sandi. Akun tidak bisa dihapus, hanya dinonaktifkan. Akun yang dinonaktifkan langsung ter-logout. Owner tidak bisa menonaktifkan atau menurunkan peran dirinya sendiri.
- **Pengaturan** (owner) — data toko, margin, tarif jasa, kapasitas, SEO. Nomor WA `0812…` otomatis diubah ke `62812…`.
- Komponen Blade: `<x-pill>`, `<x-panel>`, `<x-field>`.

### Tahap 3 — Kelas harga (inti OOP)
Folder `app/Domain/Harga/`:

| Kelas | Rumus `hitungHarga()` |
|---|---|
| `KalkulatorHarga` (abstract) | menyediakan `modalBahan()`, `ongkosJasa()`, `margin()`, `rincian()` |
| `HargaArtificial` | (modal × margin) + jasa |
| `HargaFresh` | (modal × margin × 1,10) + jasa |
| `HargaUang` | (lembar × tarif lipat) + jasa |
| `HargaPapan` | sewa rangka + modal + ongkos pasang + jasa |
| `HargaUmum` | (modal × margin) + jasa |
| `HargaFactory::untuk($produk)` | memilih kelas dari `kategori.tipe_harga` |

Tambahan di luar docs: method `rumus()` dan `rincianTambahan()`, supaya halaman bisa menunjukkan cara menghitungnya, bukan hanya angka akhir.

Angka acuan (margin 1,6, tarif Rp 10.000/jam): **DF-014 = Rp 179.200**, **DF-016 = Rp 135.560**. Kalau DF-016 memakai rumus artificial, hasilnya Rp 124.600; selisih Rp 10.960 adalah cadangan risiko layu.

Halaman uji: `/admin/produk/{id}` menampilkan komposisi, modal per bahan, rumus, nama kelas, dan rincian saran harga.

**Pemetaan pilar OOP untuk presentasi:**
- *Abstraction* — `KalkulatorHarga` tidak bisa dibuat langsung. Kelas ini hanya menetapkan bahwa setiap produk pasti punya `hitungHarga()`.
- *Inheritance* — `modalBahan()` dan `ongkosJasa()` ditulis sekali di induk. `HargaUang` dan `HargaPapan` memanggil `parent::__construct()`.
- *Polymorphism* — controller cukup memanggil `HargaFactory::untuk($produk)->rincian()`, tanpa `if` per kategori.
- *Encapsulation* — kolom `stok` di model `Bahan` tidak bisa diisi massal. Stok hanya berubah lewat `MutasiStokService`.

### Tahap 4 — Pesanan & pembayaran
- `app/Domain/Pesanan/PesananService.php` memuat semua aturan bisnis; controller tetap tipis.
- **Daftar pesanan**: tab status, urut tanggal jadi terdekat. Tanggal ditulis "Hari ini" (merah), "Besok" (amber), atau "Sen, 21 Sep". Status pengerjaan dan status bayar ditampilkan terpisah.
- **Form pesanan baru**: kontak boleh kosong, model dari katalog atau custom, DP bisa langsung dicatat.
- **Aturan status**:
  - maju hanya satu langkah (`masuk → dikerjakan → jadi → selesai`), tidak bisa melompat
  - mundur hanya boleh oleh owner
  - `selesai` ditolak selama belum lunas
  - `batal` bersifat final
- **Stok** berkurang saat status berubah ke `dikerjakan`, lewat `MutasiStokService::keluarUntukPesanan()`. Kalau status dimundurkan lalu dimajukan lagi, bahan tidak diambil dua kali.
- **Batal**: DP tetap tercatat (hangus), dan ada tawaran memasukkan buket ke Stok Produk Jadi.
- **Pembayaran**: maksimal dua kali, dan pembayaran kedua wajib melunasi sisa.

### Tahap 5 — Tiga fitur andalan
- `app/Domain/Stok/CekKelayakan.php` + `HasilCek.php`:
  - menghitung kekurangan bahan × qty, mis. "Kurang 6 tangkai mawar putih"
  - saran alternatif: buket di Stok Produk Jadi, ditambah model sekategori yang bahannya cukup ("Bahan cukup untuk 2 buket"), maksimal 5
  - badge katalog: Pre-order / Bahan habis / Ready
- `app/Domain/Jadwal/CekKapasitas.php` + `HasilKapasitas.php`:
  - beban per tanggal dibandingkan kapasitas (3 perakit × 6 jam = 18 jam)
  - bila penuh, menyarankan tanggal terdekat yang masih muat (maksimal 7 hari ke depan)
- Kolom kanan form pesanan diisi lewat AJAX (`POST /admin/pesanan/cek-kelayakan`):
  - peringatan bahan (merah) → saran model dengan tombol **Pakai** → peringatan tanggal padat (amber) dengan tombol **Pakai tanggal itu** → kotak perkiraan harga beserta rinciannya dan tombol **Pakai harga ini**
- **Peringatan tidak pernah memblokir**; tombol Simpan selalu aktif.

### Tahap 6 — Halaman publik
- Beranda:
  - hero dengan tiga angka (2023 / 6 jenis / 1–3 hari)
  - chip kategori
  - 4 produk paling sering dipesan
  - cara memesan 3 langkah
  - bagian Tentang
  - footer 4 kolom
- Katalog `/katalog` + halaman kategori `/katalog/kategori/{slug}` (dengan paragraf pengantar dari admin), teks "Menampilkan X dari Y model".
- Detail `/katalog/{slug}`:
  - daftar spesifikasi
  - tombol WhatsApp `wa.me` dengan pesan berisi nama dan kode model
  - kotak pratinjau pesan
  - 3 model serupa
- Harga hanya tampil bila `tampilkan_harga = true`; selain itu tertulis "Chat untuk harga".
- SEO:
  - komponen `<x-seo>` (title, description, canonical, Open Graph)
  - JSON-LD `Florist` di layout dan `Product` di detail (tanpa `offers`)
  - `/sitemap.xml` dan `/robots.txt` dibuat dari database
  - admin diberi `noindex`
- Data toko diambil dari tabel `pengaturan` lewat View Composer; tidak ada nomor WA yang ditulis langsung di view.

### Tahap 7 — Dashboard, pengeluaran, laporan
- **Dashboard**:
  - 4 tile: pesanan aktif, jatuh tempo hari ini, beban hari ini (7/18), bahan menipis
  - kolom kiri: antrian hari ini, buket siap dijual
  - kolom kanan: batang beban 7 hari (hijau < 85%, amber 85–100%, merah > 100%, selalu dengan angka jam), daftar bahan menipis
- **Pengeluaran** (owner): CRUD dengan filter bulan & kategori, bukti foto/PDF. Pengeluaran yang terhubung ke catatan stok masuk tidak bisa dihapus.
- **Daftar belanja**: jumlah beli = minimum + kebutuhan pesanan yang belum dikerjakan − sisa. Bisa dicetak.
- **Stok produk jadi**: catat buket untuk dipajang, tandai terjual.
- **Laporan** (owner):
  - penjualan = pesanan selesai + DP hangus, berdasarkan tanggal jadi
  - pengeluaran per kategori
  - laba sederhana
  - uang yang benar-benar diterima ditampilkan sebagai info terpisah
- **PDF** lewat dompdf: kop nama toko, alamat, dan WA. Ukuran ±25 KB.

### Tahap 8 — Rapikan
- 24 halaman (23 admin + login) diperiksa di lebar 400px:
  - tidak ada yang meluber
  - sidebar berubah jadi baris menu yang bisa digeser
  - target sentuh ≥ 40px
- `app/Support/FotoWebp.php` membuat salinan WebP (maksimal 1200px) untuk setiap foto yang diunggah, lalu disajikan lewat `<picture>`. Contoh: foto 79 KB → WebP 2 KB.
- Halaman error Indonesia: 403, 404, 419.
- `ProduksiSeeder` hanya mengisi pengaturan + 6 kategori, untuk hosting (tanpa data contoh).
- Perintah `php artisan akun:owner` membuat akun pemilik; kata sandinya diketik tersembunyi.
- `route:cache`, `config:cache`, `view:cache` sudah dicoba dan berjalan.

---

## 5. Perubahan pada dokumen

| Dokumen | Perubahan |
|---|---|
| `docs/05-SEED-DATA.md` | Tambah pesanan **DF-2609-040** (Bunga Papan × 2) dan **DF-2609-044** (Bunga Papan). Data awal hanya membuat 22 Sep berisi 4 jam, padahal docs menyebut tanggal itu harus kelebihan beban. Sekarang 22 Sep = 2 + 2 + 10 + 5 = **19 jam dari 18**. Disetujui kelompok (opsi a) |
| `docs/06-BUILD-PLAN.md` | Syarat Tahap 1 menjadi "11 pesanan". Semua butir kode dicentang; tiga butir manual Tahap 8 dibiarkan kosong |
| `docs/07-ACCEPTANCE.md` | Ditambah bagian **S. SEO** (S1–S8), sesuai permintaan `docs/08-SEO.md` A12 |
| `README.md` | Ditambah mode demo, foto produk, dan langkah deploy |

---

## 6. Keputusan yang perlu dikonfirmasi kelompok

1. **Aturan status stok.** Docs tidak mendefinisikannya. Yang dipakai: sisa ≤ ¼ minimum = **Habis**, di bawah minimum = **Menipis**. Letaknya di `Bahan::statusStok()`.
2. **Penyesuaian stok.** Karena `jumlah` mutasi selalu positif, yang dicatat adalah selisihnya, dan arahnya ditulis di keterangan ("Stok 36 → 33 (−3)").
3. **Hak akses karyawan.** Karyawan boleh menambah dan mengubah produk, kategori, dan bahan, mengikuti docs/03 §7. PRD menulis karyawan hanya "lihat katalog & bahan".
4. **Dasar tanggal laporan.** Penjualan dihitung dari tanggal jadi pesanan.
5. **Grafik di halaman Laporan tidak dibuat.** Build plan mengizinkan bagian ini dipotong.
6. **Batal bersifat final.** Pembayaran kedua wajib melunasi.
7. **`TANGGAL_DEMO`** untuk demo di laptop (lihat bagian 2).

---

## 7. Bug yang ketemu lewat pengujian (sudah diperbaiki)

| Tahap | Bug | Perbaikan |
|---|---|---|
| 4 | Ubah pesanan error 500 bila ada kolom opsional yang tidak terkirim | Kolom opsional diberi nilai kosong bawaan |
| 4 | `TANGGAL_DEMO` membuat login tidak menempel (cookie sesi dianggap kedaluwarsa) | Di mode demo, cookie sesi berlaku sampai browser ditutup |
| 5 | Atribut `data-harga` bentrok dengan `<option>` di dropdown model | Diganti `data-pakai-harga` |
| 5 | `setSelectionRange` pada input angka melempar error di Chrome | Dihapus |
| 6 | Beranda & detail meluber 12px di HP | Jarak kolom `g-5` → `g-4 g-lg-5` |
| 7 | Menu "Daftar belanja" selalu tersorot | Pola menu aktif diperbaiki |
| 7 | Tombol "Tandai terjual" tidak jalan (validasi form tambah ikut dipakai) | Aturan validasi dibedakan untuk tambah dan ubah |
| 7 | PDF 880 KB | Font subsetting → 25 KB |
| 8 | "Hari ini" di tabel tampil hitam, bukan merah (kalah oleh aturan Bootstrap) | Selektor CSS dipertajam, warna diukur ulang di Chrome |

---

## 8. Cara pengujian dilakukan

- **Uji HTTP (curl)** untuk setiap tahap: login sebagai owner/karyawan, membuka halaman, mengirim form, lalu memeriksa isi database lewat `artisan tinker`. Skenario mengikuti `docs/07-ACCEPTANCE.md` (A–I dan S).
- **JavaScript form pesanan** dijalankan di jsdom (DOM tiruan di Node) dengan panggilan AJAX ke server sungguhan: pilih model, klik Pakai, ganti tanggal, isi sewa rangka, dan seterusnya.
- **Chrome** untuk halaman publik (tampilan desktop & HP) dan untuk mengukur tata letak halaman admin di lebar 400px.
- Setelah Tahap 8, **semua uji Tahap 1–8 dijalankan ulang** untuk memastikan tidak ada yang rusak.

Skrip uji disimpan di luar repo, karena CLAUDE.md meminta tidak menambah tes otomatis. Skenarionya bisa diulang manual dengan ceklis di `docs/07-ACCEPTANCE.md`.

**Keterbatasan:**
- Panel admin belum pernah dicoba lewat login di browser sungguhan. Coba sekali secara manual, terutama form **Pesanan baru**.
- Jalur sukses `php artisan akun:owner` belum teruji otomatis, karena input sandi tersembunyi butuh terminal sungguhan.
- Satu kali uji ukuran WebP gagal saat semua uji dijalankan berurutan. Kegagalan itu tidak terulang dalam tiga kali ulang, dan penyebabnya belum ditemukan.
- S4, S7, dan S8 (Rich Results Test, share link WhatsApp, PageSpeed) baru bisa diuji setelah online.

---

## 9. Sisa pekerjaan

- [ ] Coba panel admin langsung di browser (login owner dan karyawan)
- [ ] Taruh foto `p1`…`p9` di `storage/app/public/produk/`, lalu `php artisan migrate:fresh --seed`
- [ ] Putuskan butir-butir di bagian 6
- [ ] Commit ke git
- [ ] Deploy ke hosting (langkahnya di `README.md`)
- [ ] `php artisan db:seed --class=ProduksiSeeder --force` lalu `php artisan akun:owner` untuk akun Bu Siti
- [ ] Isi Pengaturan: link Instagram/TikTok, koordinat, link Google Maps
- [ ] Uji S4, S7, S8 setelah online
- [ ] Sosialisasi ke mitra

---

## 10. Peta file

```
app/
├─ Domain/
│  ├─ Harga/      KalkulatorHarga, HargaArtificial, HargaFresh, HargaUang, HargaPapan, HargaUmum, HargaFactory
│  ├─ Jadwal/     CekKapasitas, HasilKapasitas
│  ├─ Laporan/    LaporanKeuangan
│  ├─ Pesanan/    PesananService
│  └─ Stok/       CekKelayakan, HasilCek, MutasiStokService
├─ Http/
│  ├─ Controllers/Admin/   Dashboard, Pesanan, Produk, Kategori, Bahan, MutasiStok, StokProdukJadi,
│  │                       Pengeluaran, Laporan, Pengguna, Pengaturan
│  ├─ Controllers/Publik/  Beranda, Katalog
│  ├─ Middleware/          RoleOwner
│  └─ Requests/            15 Form Request (satu per form)
├─ Models/                 14 model
└─ Support/FotoWebp.php
database/
├─ migrations/             14 tabel
└─ seeders/                DatabaseSeeder (data contoh), ProduksiSeeder (untuk hosting)
resources/views/
├─ layouts/                publik, admin, aset
├─ components/             pill, panel, field, alert, tile, seo, kartu-produk, foto-produk, ikon-wa
├─ publik/                 beranda, katalog, detail, sitemap
├─ admin/                  dashboard, pesanan/, produk/, kategori/, bahan/, mutasi-stok/,
│                          stok-produk-jadi/, pengeluaran/, laporan/, pengguna/, pengaturan/
├─ auth/login.blade.php
└─ errors/                 403, 404, 419
lang/id/                   auth, validation, pagination (+ id.json)
public/css/app.css         token warna + semua gaya
routes/                    web.php, auth.php, console.php (akun:owner)
```

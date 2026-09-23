# CLAUDE.md — Panduan Kerja untuk Claude Code

Baca file ini lebih dulu sebelum menulis kode apa pun di repo ini.

## Apa yang sedang dibangun

Sistem informasi untuk **Deepsea Florist Home Store**, toko bunga di Cibogo, Subang. Dua bagian dalam satu aplikasi Laravel:

1. **Halaman publik** — company profile + katalog produk (tanpa keranjang, tanpa checkout)
2. **Panel admin** — pencatatan pesanan, stok bahan, pengeluaran, dan laporan

Ini project mata kuliah **Pemrograman Berorientasi Objek 1 (PBO 1)** di Politeknik Negeri Subang, dikerjakan bertiga oleh mahasiswa tingkat 2. Mitranya nyata dan sistemnya akan benar-benar dipakai.

## Dokumen yang wajib dibaca

| File | Isi |
|---|---|
| `docs/01-PRD.md` | Masalah mitra, pengguna, ruang lingkup, tiga fitur andalan |
| `docs/02-DATABASE.md` | 14 tabel, kolom, relasi, aturan migrasi |
| `docs/03-ARCHITECTURE.md` | Struktur folder, kelas OOP, service, alur request |
| `docs/04-UI-GUIDE.md` | Warna, huruf, komponen, 8 layar |
| `docs/05-SEED-DATA.md` | Data contoh yang harus dipakai seeder |
| `docs/06-BUILD-PLAN.md` | Urutan pengerjaan per tahap |
| `docs/07-ACCEPTANCE.md` | Ceklis "selesai" tiap fitur |

Kalau ada pertanyaan yang jawabannya tidak ada di dokumen mana pun, **tanya dulu**, jangan mengarang fitur.

## Stack yang dikunci

- PHP 8.2+, **Laravel 12**
- MySQL 8 (XAMPP saat development)
- **Blade** + **Bootstrap 5** (via CDN atau Vite, bebas) + CSS variabel sendiri
- Autentikasi: **Laravel Breeze versi Blade**, route `register` dimatikan
- PDF: **dompdf** (`barryvdh/laravel-dompdf`)
- Tidak ada: Filament, Nova, Livewire, Inertia, React, Vue, Tailwind

**Kenapa bukan Filament?** Silabus mata kuliah menuntut praktik Controller–Model–Blade dan CRUD manual. Filament menyembunyikan semuanya dan nilainya ada di situ. Jangan mengusulkan Filament lagi.

## Aturan menulis kode

- **Bahasa Indonesia untuk nama tabel, kolom, route, dan variabel domain** (`pesanan`, `bahan`, `tanggal_jadi`). Nama kelas dan method tetap konvensi Laravel (`PesananController`, `store`, `index`).
- **Kode untuk pemula.** Hindari pola rumit: tidak perlu repository pattern, tidak perlu event/listener, tidak perlu queue. Controller tipis → Service class → Eloquent.
- **Komentar seperlunya saja**, hanya untuk bagian yang tidak jelas dari namanya. Jangan mengomentari tiap baris.
- **Form Request** untuk validasi, bukan validasi di dalam controller.
- **Satu file Blade per halaman**, komponen berulang jadi `resources/views/components/`.
- Jangan menulis tes otomatis kecuali diminta — kelompok ini belum sampai materi testing.

## Aturan bisnis yang tidak boleh dilanggar

Ini hasil wawancara dengan pemilik. Jangan diubah tanpa persetujuan.

1. **Status pesanan hanya lima**: `masuk` → `dikerjakan` → `jadi` → `selesai`, plus `batal`. Mundur ke status sebelumnya hanya boleh oleh role `owner`.
2. **Stok bahan berkurang saat status berubah ke `dikerjakan`**, bukan saat pesanan masuk.
3. **DP 50%**, pelunasan wajib sebelum barang diserahkan. Maksimal dua kali pembayaran.
4. **Status `selesai` diblokir** selama `total_dibayar < total`.
5. **Pesanan batal**: DP tetap tercatat sebagai pemasukan (hangus), dan buket yang terlanjur jadi masuk ke tabel `stok_produk_jadi`.
6. **Harga tidak tampil di katalog publik** kecuali `produk.tampilkan_harga = true` (default `false`).
7. **Tidak ada pendaftaran akun publik.** Akun karyawan dibuat owner dari panel.
8. **Karyawan tidak bisa** melihat laporan keuangan, pengeluaran, dan manajemen pengguna.
9. **Kontak pelanggan boleh kosong.** Jangan dijadikan field wajib — saat ramai, form yang rewel bikin owner balik nyatat di kertas.
10. **Peringatan tidak memblokir.** Bahan kurang dan tanggal penuh hanya memberi tahu; tombol simpan tetap aktif. Yang memutuskan tetap manusia.

## Yang sengaja TIDAK dibuat

Jangan menambahkan ini walaupun kelihatan "melengkapi":

- Keranjang belanja, checkout, payment gateway
- Chatbot, rekomendasi berbasis AI/ML
- Aplikasi mobile terpisah
- Integrasi API WhatsApp / Instagram / TikTok (cukup link `wa.me` biasa)
- Multi-cabang, multi-tenant
- Notifikasi email/SMS

## Perintah yang sering dipakai

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
npm install && npm run dev   # kalau memakai Vite
```

**Setiap kali `public/css/app.css` (atau berkas lain di daftar `ASET` pada `public/sw.js`) berubah, naikkan `VERSI` di `public/sw.js`** (`deepsea-v1` → `deepsea-v2`). Service worker panel admin menyimpan aset itu di cache; kalau lupa, HP pemilik tetap menampilkan tampilan lama. Lihat `docs/10-PWA-ADMIN.md`.

## Cara kerja yang diharapkan

- Kerjakan **satu tahap dari `docs/06-BUILD-PLAN.md`** per sesi, jangan lompat.
- Setelah satu tahap selesai, jalankan `php artisan migrate:fresh --seed`, buka halamannya, pastikan tidak error, baru lanjut.
- Kalau menemukan kebutuhan yang bertentangan dengan dokumen, **berhenti dan bilang**, jangan diam-diam mengubah rancangan.

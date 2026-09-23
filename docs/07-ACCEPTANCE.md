# Ceklis Penerimaan

Skenario yang harus jalan sebelum sebuah fitur dianggap selesai. Semua memakai data dari `docs/05-SEED-DATA.md`, dengan tanggal acuan **19 September 2026**.

Jalankan manual di browser. Belum perlu tes otomatis.

---

## A. Autentikasi & hak akses

| # | Langkah | Hasil yang benar |
|---|---|---|
| A1 | Buka `/register` | 404 — route sudah dihapus |
| A2 | Login sebagai karyawan, buka `/admin/laporan` | Ditolak 403 |
| A3 | Login sebagai karyawan, lihat sidebar | Menu Pengeluaran, Laporan, Pengguna, Pengaturan tidak tampil |
| A4 | Login sebagai owner | Semua menu tampil |

## B. Stok

| # | Langkah | Hasil yang benar |
|---|---|---|
| B1 | Buka Bahan & stok | Mawar putih berstatus **Habis** (sisa 6, minimum 24); Baby's breath **Menipis** |
| B2 | Kolom "Catatan umur" | Terisi hanya untuk bahan fresh; artificial tertulis "Tidak layu" |
| B3 | Catat belanja masuk 30 tangkai mawar putih | `bahan.stok` jadi 36, muncul satu baris baru di mutasi stok |
| B4 | Coba ubah `bahan.stok` dari controller lain | Tidak ada jalannya — hanya lewat `MutasiStokService` |

## C. Cek kelayakan & saran alternatif

| # | Langkah | Hasil yang benar |
|---|---|---|
| C1 | Pesanan baru → pilih **DF-008 Buket Mawar Putih Medium** | Peringatan merah: "Kurang 6 tangkai mawar putih" |
| C2 | Lihat panel saran | Muncul maksimal 5 model sekategori yang bahannya cukup + Buket Putih Wrap Hitam dari Stok Produk Jadi |
| C3 | Klik "Pakai" pada salah satu saran | Field Model berganti ke produk itu, peringatan hilang |
| C4 | Pilih produk tanpa komposisi (mis. DF-040 Buket Snack Cokelat) | Tidak ada peringatan; harga diisi manual; sistem tidak error |
| C5 | Tombol Simpan saat ada peringatan | **Tetap aktif** — peringatan tidak memblokir |

## D. Kalkulator harga

| # | Langkah | Hasil yang benar |
|---|---|---|
| D1 | Pilih DF-014 (artificial) | Rincian tampil: modal bahan, margin, ongkos jasa, saran harga |
| D2 | Pilih DF-016 (fresh) | Angkanya memakai rumus fresh (ada cadangan 10%), bukan rumus artificial |
| D3 | Pilih DF-030 Bunga Papan | Rumusnya memakai sewa rangka + ongkos pasang |
| D4 | Ubah harga di form secara manual | Angka manual yang tersimpan, bukan saran sistem |
| D5 | Ubah `margin_default` di Pengaturan | Saran harga ikut berubah tanpa mengubah kode |

## E. Kapasitas

| # | Langkah | Hasil yang benar |
|---|---|---|
| E1 | Pesanan baru, tanggal jadi **22 September 2026** | Peringatan amber: beban melebihi 18 jam |
| E2 | Isi peringatan | Menyebut tanggal terdekat yang masih muat |
| E3 | Tanggal jadi **25 September 2026** | Tidak ada peringatan |
| E4 | Dashboard, batang beban 7 hari | Selasa 22 berwarna merah dan ada angka jamnya |

## F. Alur pesanan

| # | Langkah | Hasil yang benar |
|---|---|---|
| F1 | Buat pesanan baru, isi seperlunya, **kontak dikosongkan** | Tersimpan, tidak ada error validasi |
| F2 | Ubah status ke `dikerjakan` | Stok bahan berkurang sesuai komposisi; muncul baris mutasi keluar dengan `pesanan_id` terisi |
| F3 | Ubah status ke `selesai` saat baru bayar DP | **Ditolak**, ada pesan bahwa pelunasan belum masuk |
| F4 | Input pelunasan, lalu `selesai` | Berhasil; `total_dibayar` = `total` |
| F5 | Ubah pesanan lain ke `batal` | Muncul tawaran memasukkan buket ke Stok Produk Jadi |
| F6 | Login karyawan, coba mundurkan status dari `jadi` ke `dikerjakan` | Ditolak — hanya owner |
| F7 | Kode pesanan baru | Otomatis berformat `DF-YYMM-NNN`, tidak bentrok |

## G. Halaman publik

| # | Langkah | Hasil yang benar |
|---|---|---|
| G1 | Buka katalog | 10 produk tampil, tidak ada harga yang terlihat, tiap kartu menulis "Chat untuk harga" |
| G2 | Badge DF-008 | **Bahan habis** (komposisinya tidak terpenuhi) |
| G3 | Badge DF-021 | **Ready** |
| G4 | Buka detail DF-014, klik tombol WhatsApp | Membuka `wa.me` dengan pesan berisi "Buket Mawar Biru Navy (DF-014)" |
| G5 | Ubah `link_ig` di Pengaturan | Link di footer ikut berubah tanpa menyentuh Blade |
| G6 | Buka beranda di lebar 400px | Tidak ada yang meluber ke samping |
| G7 | Buka `/tentang` | Halaman terbuka, judul dan meta description berbeda dari beranda |
| G8 | Buka `/kontak` | Alamat, jam buka, tombol WhatsApp, dan peta tampil |
| G9 | Ubah kolom "tentang" di panel Pengaturan | Isi halaman `/tentang` ikut berubah tanpa menyentuh Blade |
| G10 | Buka `/sitemap.xml` | `/tentang` dan `/kontak` ikut terdaftar |
| G11 | Buka beranda di lebar 400px | Keempat menu (Beranda, Katalog, Tentang, Kontak) terlihat dan bisa diklik |

## F. FAQ dan latar Tentang (dari `docs/09-FAQ-DAN-HERO.md`)

| # | Periksa | Hasil yang benar |
|---|---|---|
| F1 | Buka `/faq` | 12 pertanyaan tampil, semua bisa dibuka-tutup |
| F2 | Matikan JavaScript, muat ulang `/faq` | Accordion tetap berfungsi dan jawaban tetap terbaca |
| F3 | Lihat source `/faq` | Seluruh teks jawaban ada di HTML, bukan dimuat JavaScript |
| F4 | Tambah satu FAQ lewat panel admin | Langsung muncul di `/faq` dan ikut masuk data terstruktur |
| F5 | Nonaktifkan satu FAQ | Hilang dari halaman dan dari schema |
| F6 | Buka `/sitemap.xml` | URL `/faq` terdaftar |
| F7 | Buka `/tentang` | Latar foto tampil, judul terbaca jelas |
| F8 | Buka `/tentang` di lebar 400px | Memakai berkas mobile, teks tidak menumpuk foto |
| F9 | Periksa jaringan di DevTools | Yang dimuat berkas `.webp`, bukan `.jpg` |

## P. PWA panel admin (dari `docs/10-PWA-ADMIN.md`)

| # | Periksa | Hasil yang benar |
|---|---|---|
| P1 | Buka `/admin` di Chrome HP | Muncul tawaran "Instal aplikasi", atau tersedia di menu |
| P2 | Pasang, lalu buka dari ikon layar utama | Terbuka tanpa address bar, warna status bar rose |
| P3 | Tekan lama ikonnya di Android | Muncul pintasan "Pesanan baru" |
| P4 | Buka halaman Bahan & stok, ubah stok dari laptop, muat ulang di HP | Angka **langsung berubah** — bukan angka lama dari cache |
| P5 | Matikan internet, buka ikon aplikasi | Muncul halaman "Tidak ada koneksi", bukan layar putih |
| P6 | Matikan internet, coba simpan pesanan | Gagal dengan jelas. **Tidak boleh** pura-pura tersimpan |
| P7 | Buka halaman publik `/` di HP | **Tidak** muncul tawaran instal aplikasi |
| P8 | Ubah isi `app.css`, naikkan `VERSI`, muat ulang | Tampilan baru langsung terpakai |
| P9 | DevTools → Application → Manifest | Tidak ada error, semua ikon terbaca |

## H. Laporan

| # | Langkah | Hasil yang benar |
|---|---|---|
| H1 | Laporan September 2026 | Total penjualan = jumlah pesanan berstatus `selesai` + DP hangus dari pesanan batal |
| H2 | Total pengeluaran September | 5.970.000 (sesuai seeder) |
| H3 | Klik Cetak PDF | File terunduh, isinya rapi, ada kop nama toko |

## I. Tampilan

| # | Periksa | Hasil yang benar |
|---|---|---|
| I1 | Semua warna | Diambil dari CSS variable, tidak ada hex langsung di Blade |
| I2 | Badge status | Selalu warna **dan** tulisan |
| I3 | Ikon | Tidak ada emoji dipakai sebagai ikon |
| I4 | Teks antarmuka | Seluruhnya bahasa Indonesia |
| I5 | Panel admin di HP | Sidebar berubah jadi baris menu, tabel bisa digeser |
| I6 | Angka di kolom tabel | Rata kanan dan sejajar (tabular-nums) |

## S. SEO (dari `docs/08-SEO.md` bagian A12)

| # | Periksa | Hasil yang benar |
|---|---|---|
| S1 | Lihat source halaman detail produk | Ada `<title>`, meta description, canonical, Open Graph, JSON-LD Product |
| S2 | Buka `/sitemap.xml` | XML valid, berisi seluruh produk aktif |
| S3 | Buka `/robots.txt` | `/admin` diblokir, ada baris Sitemap |
| S4 | Rich Results Test untuk beranda | Terdeteksi sebagai `Florist`, tanpa error |
| S5 | Ubah `meta_deskripsi_default` di Pengaturan | Berubah di semua halaman tanpa mengubah kode |
| S6 | Semua `<img>` di katalog | Punya `alt` yang terisi, bukan kosong |
| S7 | Share link produk ke WhatsApp | Muncul foto, judul, dan deskripsi |
| S8 | PageSpeed Insights versi HP | Skor performa minimal 70 |

S4, S7, dan S8 baru bisa diuji setelah website online.

---

## Kesalahan yang sering terjadi — periksa khusus

1. **Stok diubah langsung** dari `PesananController` tanpa lewat `MutasiStokService` → riwayat mutasi jadi bohong
2. **Total pesanan dihitung di Blade** → pindahkan ke model/accessor
3. **Nomor WhatsApp ditulis langsung di view** → harus dari `pengaturan.no_wa`
4. **Produk tanpa komposisi bikin error** → harus dilewati dengan aman
5. **`float` untuk kolom uang** → wajib `decimal(12,2)`
6. **Status bisa lompat** dari `masuk` langsung ke `selesai` → stok tidak pernah berkurang
7. **Register masih bisa diakses** lewat URL langsung

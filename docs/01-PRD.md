# PRD — Sistem Informasi Deepsea Florist Home Store

Versi 1.0 · 22 September 2026 · Mata Kuliah PBO 1, Politeknik Negeri Subang

---

## 1. Profil mitra

| Item | Keterangan |
|---|---|
| Nama usaha | Deepsea Florist Home Store |
| Pemilik | Siti Romlah |
| Berdiri | 2023 |
| Alamat | Jl. Raya Cinangsi RT.01/RW.01, Cinangsi, Kec. Cibogo, Kabupaten Subang, Jawa Barat 41211 |
| Kontak pemesanan | WhatsApp 0812-9683-6363 |
| Media sosial | Instagram `marya_florist` (akan diganti jadi Deepsea Florist), TikTok `@deepseaflorist21_` |
| Karyawan | ± 3 orang perakit |
| Kategori produk | Buket artificial, buket fresh, buket uang, buket snack, bunga papan, dekorasi |
| Volume pesanan | Hari biasa 1–5/hari; musim wisuda & hari besar 5–30/hari |
| Pembayaran | DP 50% lalu pelunasan; transfer, QRIS, atau tunai |
| Pencatatan saat ini | **Tidak ada.** Semua pesanan hanya tersimpan di chat WhatsApp dan ingatan pemilik |

## 2. Masalah yang diselesaikan

Diambil dari wawancara langsung, bukan asumsi:

| # | Temuan wawancara | Akibatnya | Dijawab oleh |
|---|---|---|---|
| 1 | Belum ada pembukuan sama sekali | Tidak tahu untung-rugi, tidak ada riwayat | Modul Pesanan, Pembayaran, Pengeluaran, Laporan PDF |
| 2 | Pesanan hanya diingat, datang dari WA/IG/TikTok | Rawan lupa saat 30 pesanan sehari | Tiap pesanan punya kode, deadline, status, sumber |
| 3 | Waktu pengerjaan sering keteteran | Pesanan terlambat jadi | Dashboard urut deadline + peringatan kapasitas |
| 4 | Stok sulit dipantau, item terlalu banyak | Baru ketahuan habis saat mau dikerjakan | Modul Bahan + Mutasi Stok + peringatan stok minimum |
| 5 | Permintaan pelanggan sering beda dengan stok | Owner menawarkan alternatif dari ingatan | Cek kelayakan + saran model alternatif |
| 6 | Bunga fresh dikulak seminggu sekali, tahan ±2 minggu | Bunga layu = rugi yang tak terlihat | Tanggal kadaluarsa di mutasi stok masuk |
| 7 | Harga dihitung di kepala, dari bahan + kerumitan | Hanya owner yang bisa memberi harga | Kalkulator harga otomatis (tetap bisa ditimpa) |
| 8 | Pesanan batal: DP hangus, buket jadi dipajang | Barang jadi tidak tercatat | Tabel Stok Produk Jadi |
| 9 | Produk tidak punya nama baku, pesan dari foto | Sulit merujuk model tertentu | Nama + kode model dibuat owner lewat CMS |
| 10 | Harga tidak diekspos ke publik | Negosiasi tetap lewat chat | Katalog menulis "Chat untuk harga" |

## 3. Pengguna

| Peran | Siapa | Yang bisa dilakukan |
|---|---|---|
| **Pengunjung** | Calon pelanggan | Lihat beranda, katalog, detail produk; klik tombol WhatsApp |
| **Owner** | Siti Romlah | Semua fitur, termasuk laporan keuangan, pengeluaran, kelola akun |
| **Karyawan** | 3 perakit | Input & ubah status pesanan, catat mutasi stok, lihat katalog & bahan |

Karyawan **tidak bisa**: laporan, pengeluaran, manajemen pengguna, pengaturan sistem.

## 4. Ruang lingkup

### 4.1 Halaman publik
- Beranda: hero, kategori, produk terlaris, cara memesan, footer
- Katalog: filter kategori, grid produk, badge Ready / Pre-order / Bahan habis
- Detail produk: foto besar, spesifikasi, kode model, tombol WhatsApp dengan pesan otomatis
- Tentang & Kontak (boleh digabung ke beranda kalau waktunya mepet)

### 4.2 Panel admin
- Dashboard
- Pesanan: daftar, tambah, detail, ubah status, pembayaran
- Katalog produk: CRUD produk, kategori, foto, komposisi bahan
- Bahan & stok: CRUD bahan, mutasi stok, daftar belanja
- Produk siap jual (stok barang jadi)
- Pengeluaran
- Laporan: penjualan, pengeluaran, laba sederhana — cetak PDF
- Pengguna & Pengaturan

## 5. Tiga fitur andalan

Ini yang membedakan sistem ini dari aplikasi penjualan biasa. **Jangan dikorbankan** kalau waktu mepet — potong fitur lain.

### 5.1 Cek Kelayakan Pesanan + Saran Model Alternatif

Buket dirakit dari bahan, bukan diambil dari rak. Maka pertanyaan sistem bukan "barangnya masih ada?" tapi "model ini masih bisa dirakit atau tidak?"

**Alur:**
1. Owner memilih produk di form Pesanan Baru
2. Sistem menjumlahkan kebutuhan bahan dari `produk_bahan` × qty
3. Dibandingkan dengan `bahan.stok`
4. Kalau ada yang kurang → tampilkan nama bahan + jumlah kekurangan
5. Sistem mencari produk lain **pada kategori yang sama** yang seluruh bahannya cukup, ditambah isi `stok_produk_jadi` yang berstatus `tersedia`
6. Tampilkan maksimal 5 saran

**Contoh:** Buket Mawar Putih Medium butuh 12 tangkai mawar putih, stok 6 → "Kurang 6 tangkai mawar putih." Saran: Buket Krisan Putih (cukup untuk 3 buket), Buket Tulip Pink (cukup untuk 5 buket), Buket Putih Wrap Hitam (1 sudah jadi, siap ambil).

### 5.2 Kalkulator Harga dari Modal Bahan + Kerumitan

```
modalBahan   = Σ (produk_bahan.jumlah × bahan.harga_beli_terakhir)
ongkosJasa   = produk.estimasi_jam × pengaturan.tarif_jasa_per_jam × produk.faktor_kerumitan
saranHarga   = (modalBahan × pengaturan.margin_default) + ongkosJasa
```

Rumusnya **berbeda per kategori** — lihat `docs/03-ARCHITECTURE.md` bagian kelas harga. Hasilnya cuma **saran**; field harga di form tetap bisa diubah manual.

Produk yang belum punya komposisi tetap bisa dipesan, harganya diisi manual seperti cara lama.

### 5.3 Peringatan Kapasitas Pengerjaan

```
beban(tanggal) = Σ estimasi_jam pesanan berstatus masuk|dikerjakan|jadi dengan tanggal_jadi = tanggal
kapasitas      = pengaturan.jumlah_perakit × pengaturan.jam_kerja_per_hari
```

Kalau `beban + estimasi_jam pesanan baru > kapasitas`, tampilkan peringatan dan sarankan tanggal terdekat yang masih muat. **Tidak memblokir penyimpanan.**

## 6. Risiko utama

Ketiga fitur di atas berdiri di atas data komposisi yang harus diisi pemilik. Karena itu sistem **wajib tetap berjalan** meskipun komposisi belum lengkap:

- Produk tanpa `produk_bahan` → lewati cek kelayakan, harga diisi manual
- Produk tanpa `estimasi_jam` → anggap 0 jam untuk perhitungan kapasitas
- Target realistis: komposisi diisi untuk 5–10 model terlaris saja

## 7. Definisi sukses

- Owner bisa mencatat satu pesanan lengkap dari nol dalam **di bawah 1 menit**
- Semua halaman admin enak dipakai di HP (owner pakai HP dan laptop bergantian)
- Laporan bulan berjalan bisa dicetak jadi PDF
- Sistem tetap berguna walau komposisi bahan baru terisi sebagian

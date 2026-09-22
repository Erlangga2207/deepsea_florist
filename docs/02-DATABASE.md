# Rancangan Database

14 tabel. Semua nama tabel dan kolom memakai bahasa Indonesia. Engine InnoDB, charset `utf8mb4`.

Urutan migrasi penting karena foreign key — ikuti urutan di bawah.

---

## 1. users

Bawaan Laravel Breeze, ditambah kolom role.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | varchar | |
| email | varchar unique | |
| password | varchar | |
| role | enum('owner','karyawan') | default `karyawan` |
| is_aktif | boolean | default `true` |
| timestamps | | |

> Hapus route `register` di `routes/auth.php`. Akun dibuat lewat menu Pengguna.

## 2. kategori

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | varchar | |
| slug | varchar unique | |
| tipe_harga | enum('artificial','fresh','uang','papan','umum') | menentukan kelas kalkulator harga |
| urutan | int | default 0 |
| timestamps | | |

`tipe_harga` inilah yang dipakai `HargaFactory` untuk memilih kelas turunan. Jangan pakai nama kategori untuk itu — nama bisa diubah owner.

## 3. produk

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| kategori_id | FK → kategori | |
| kode | varchar unique | contoh `DF-014`, dibuat otomatis |
| nama | varchar | dibuat sendiri oleh owner |
| slug | varchar unique | |
| deskripsi | text nullable | |
| foto_utama | varchar nullable | path di `storage/app/public/produk` |
| harga_dasar | decimal(12,2) nullable | patokan internal |
| tampilkan_harga | boolean | **default false** |
| estimasi_jam | decimal(5,2) | default 0, lama pengerjaan per unit |
| faktor_kerumitan | tinyint | 1–3, default 1 |
| status | enum('ready','preorder') | default `ready` |
| is_aktif | boolean | default true |
| timestamps | | |

## 4. produk_foto

| Kolom | Tipe |
|---|---|
| id | bigint PK |
| produk_id | FK → produk, cascade delete |
| file | varchar |
| urutan | int default 0 |
| timestamps | |

## 5. bahan

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | varchar | |
| jenis | enum('artificial','fresh','pendukung') | |
| satuan | varchar | tangkai, lembar, meter, roll, pcs |
| stok | decimal(10,2) | default 0, diperbarui lewat mutasi |
| stok_minimum | decimal(10,2) | default 0 |
| harga_beli_terakhir | decimal(12,2) | default 0 |
| dijual_eceran | boolean | default false |
| harga_eceran | decimal(12,2) nullable | |
| is_aktif | boolean | default true |
| timestamps | | |

> Bunga fresh yang juga dijual per tangkai memakai `dijual_eceran = true`. Jangan membuat tabel produk terpisah untuk itu — nanti stoknya punya dua sumber kebenaran dan pasti beda angka.

## 6. produk_bahan (komposisi)

| Kolom | Tipe |
|---|---|
| id | bigint PK |
| produk_id | FK → produk, cascade delete |
| bahan_id | FK → bahan |
| jumlah | decimal(10,2) |
| timestamps | |

Unique index gabungan `(produk_id, bahan_id)`.

## 7. pelanggan

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| nama | varchar | |
| kontak | varchar nullable | **boleh kosong** |
| sumber | enum('wa','ig','tiktok','langsung') | default `wa` |
| catatan | text nullable | |
| timestamps | | |

## 8. pesanan

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| kode | varchar unique | format `DF-YYMM-NNN`, contoh `DF-2609-041` |
| pelanggan_id | FK → pelanggan | |
| user_id | FK → users | yang menginput |
| tanggal_pesan | date | |
| tanggal_jadi | date | deadline, dasar urutan dashboard |
| metode_ambil | enum('ambil','antar') | default `ambil` |
| alamat_antar | text nullable | |
| subtotal | decimal(12,2) default 0 | |
| biaya_tambahan | decimal(12,2) default 0 | ukuran custom / kerumitan |
| ongkir | decimal(12,2) default 0 | diisi manual |
| total | decimal(12,2) default 0 | subtotal + biaya_tambahan + ongkir |
| total_dibayar | decimal(12,2) default 0 | akumulasi dari tabel pembayaran |
| status | enum('masuk','dikerjakan','jadi','selesai','batal') | default `masuk` |
| catatan | text nullable | |
| timestamps | | |

Index pada `tanggal_jadi` dan `status`.

## 9. pesanan_item

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| pesanan_id | FK → pesanan, cascade delete | |
| produk_id | FK → produk **nullable** | kosong untuk model custom di luar katalog |
| nama_item | varchar | disalin dari produk atau diketik manual |
| ukuran | varchar nullable | default "Normal" |
| warna | varchar nullable | |
| kartu_ucapan | text nullable | |
| qty | int default 1 | |
| harga | decimal(12,2) | harga satuan yang disepakati |
| subtotal | decimal(12,2) | qty × harga |
| timestamps | | |

## 10. pembayaran

| Kolom | Tipe |
|---|---|
| id | bigint PK |
| pesanan_id | FK → pesanan, cascade delete |
| jenis | enum('dp','pelunasan') |
| jumlah | decimal(12,2) |
| metode | enum('transfer','qris','cash') |
| tanggal | date |
| timestamps | |

Maksimal dua baris per pesanan. Setiap kali disimpan, perbarui `pesanan.total_dibayar`.

## 11. mutasi_stok

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| bahan_id | FK → bahan | |
| pesanan_id | FK → pesanan **nullable** | diisi bila keluar karena pesanan |
| pengeluaran_id | FK → pengeluaran **nullable** | diisi bila masuk karena belanja |
| user_id | FK → users | |
| tipe | enum('masuk','keluar','penyesuaian') | |
| jumlah | decimal(10,2) | selalu positif; arah ditentukan `tipe` |
| tanggal | date | |
| tanggal_kadaluarsa | date nullable | hanya untuk bahan jenis `fresh` |
| keterangan | varchar nullable | contoh "kulakan Lembang", "bunga layu" |
| timestamps | | |

**Aturan:** `bahan.stok` **hanya** boleh berubah lewat pembuatan baris mutasi. Jangan pernah `update` langsung ke kolom stok dari controller lain.

## 12. pengeluaran

| Kolom | Tipe |
|---|---|
| id | bigint PK |
| user_id | FK → users |
| tanggal | date |
| kategori | enum('bahan','gaji','sewa','listrik','wifi','lain') |
| nominal | decimal(12,2) |
| keterangan | varchar nullable |
| bukti | varchar nullable |
| timestamps | |

## 13. stok_produk_jadi

Buket yang sudah jadi dan siap dijual langsung.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| produk_id | FK → produk nullable | |
| pesanan_id | FK → pesanan nullable | asal, bila dari pesanan batal |
| nama | varchar | |
| foto | varchar nullable | |
| harga_jual | decimal(12,2) nullable | |
| asal | enum('batal','produksi') | |
| status | enum('tersedia','terjual') | default `tersedia` |
| timestamps | | |

## 14. pengaturan

Satu baris saja (id = 1). Dibuat oleh seeder.

| Kolom | Tipe | Nilai awal |
|---|---|---|
| id | bigint PK | 1 |
| nama_toko | varchar | Deepsea Florist Home Store |
| alamat | text | Jl. Raya Cinangsi RT.01/RW.01, Cinangsi, Kec. Cibogo, Kabupaten Subang 41211 |
| jam_buka | varchar | Senin–Sabtu, 08.00–17.00 |
| no_wa | varchar | 6281296836363 |
| link_ig | varchar | |
| link_tiktok | varchar | |
| tentang | text | |
| jumlah_perakit | tinyint | 3 |
| jam_kerja_per_hari | decimal(4,2) | 6 |
| margin_default | decimal(5,2) | 1.60 |
| tarif_jasa_per_jam | decimal(12,2) | 10000 |

> Link media sosial **wajib** diambil dari tabel ini, jangan ditulis langsung di Blade. Nama akun Instagram mitra akan berganti.

---

## Relasi ringkas

```
kategori 1─* produk 1─* produk_foto
produk   1─* produk_bahan *─1 bahan
bahan    1─* mutasi_stok
pelanggan 1─* pesanan 1─* pesanan_item *─0..1 produk
pesanan  1─* pembayaran
pesanan  1─* mutasi_stok
pesanan  1─* stok_produk_jadi
pengeluaran 1─* mutasi_stok
users    1─* pesanan, pengeluaran, mutasi_stok
```

## Catatan implementasi

- Semua FK pakai `foreignId(...)->constrained()`. Cascade delete hanya untuk `produk_foto`, `produk_bahan`, `pesanan_item`, `pembayaran`.
- Kolom uang pakai `decimal(12,2)`, jangan `float`.
- `pesanan.kode` dibuat di Model lewat `booted()` + `creating`, bukan di controller.
- Tambahkan `softDeletes` hanya pada `produk` dan `bahan`; sisanya tidak perlu.

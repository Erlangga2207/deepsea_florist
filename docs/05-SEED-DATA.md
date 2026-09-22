# Data Seeder

Semua angka di bawah adalah **data contoh yang masuk akal untuk Subang**, bukan data asli mitra. Ganti setelah data asli dari Bu Siti masuk. Tetap pakai data ini apa adanya supaya tampilan sistem sama dengan mockup dan enak dipakai demo.

Jangan pernah memakai "Produk 1", "Lorem ipsum", atau "John Doe".

---

## users

| name | email | password | role |
|---|---|---|---|
| Siti Romlah | owner@deepseaflorist.test | password | owner |
| Karyawan Deepsea | karyawan@deepseaflorist.test | password | karyawan |

## kategori

| nama | slug | tipe_harga |
|---|---|---|
| Buket artificial | buket-artificial | artificial |
| Buket fresh | buket-fresh | fresh |
| Buket uang | buket-uang | uang |
| Buket snack | buket-snack | umum |
| Bunga papan | bunga-papan | papan |
| Dekorasi | dekorasi | umum |

## bahan

| nama | jenis | satuan | stok | minimum | harga_beli_terakhir | eceran |
|---|---|---|---|---|---|---|
| Mawar putih | fresh | tangkai | 6 | 24 | 4.000 | ya, 7.000 |
| Mawar merah | fresh | tangkai | 36 | 24 | 4.000 | ya, 7.000 |
| Lily pink | fresh | tangkai | 18 | 12 | 12.000 | ya, 18.000 |
| Krisan putih | fresh | tangkai | 40 | 20 | 3.500 | ya, 6.000 |
| Baby's breath | fresh | ikat | 9 | 20 | 15.000 | tidak |
| Gerbera oranye | fresh | tangkai | 24 | 15 | 5.000 | ya, 8.000 |
| Carnation | fresh | tangkai | 30 | 15 | 4.500 | tidak |
| Eucalyptus | fresh | ikat | 12 | 8 | 10.000 | tidak |
| Daun pakis | fresh | ikat | 14 | 8 | 7.000 | tidak |
| Mawar artificial biru navy | artificial | tangkai | 85 | 40 | 3.000 | tidak |
| Mawar artificial pink | artificial | tangkai | 96 | 40 | 3.000 | tidak |
| Tulip artificial pink | artificial | tangkai | 120 | 40 | 3.500 | tidak |
| Kertas wrapping | pendukung | lembar | 45 | 25 | 2.500 | tidak |
| Kertas kraft cokelat | pendukung | lembar | 12 | 30 | 2.000 | tidak |
| Plastik wrapping bening | pendukung | roll | 3 | 2 | 25.000 | tidak |
| Pita satin 2,5 cm | pendukung | meter | 18 | 10 | 3.000 | tidak |
| Tali rami | pendukung | meter | 22 | 10 | 2.000 | tidak |
| Floral foam | pendukung | pcs | 14 | 6 | 8.000 | tidak |
| Floral tape | pendukung | roll | 5 | 3 | 12.000 | tidak |
| Kartu ucapan | pendukung | pcs | 60 | 25 | 1.500 | tidak |

Bahan jenis `fresh` diberi mutasi masuk tanggal **12 September 2026** dengan `tanggal_kadaluarsa` 14 hari setelahnya. Bahan `artificial` dan `pendukung` masuk tanggal **28 Agustus / 5 September 2026** tanpa kadaluarsa.

## produk

| kode | nama | kategori | harga_dasar | estimasi_jam | kerumitan | status | foto |
|---|---|---|---|---|---|---|---|
| DF-001 | Buket Lily Pink | Buket fresh | 185.000 | 2 | 2 | ready | p1 |
| DF-002 | Buket Gerbera Oranye | Buket fresh | 145.000 | 1.5 | 1 | ready | p2 |
| DF-008 | Buket Mawar Putih Medium | Buket fresh | 158.000 | 2 | 2 | ready | p4 |
| DF-014 | Buket Mawar Biru Navy | Buket artificial | 225.000 | 2 | 2 | preorder | p3 |
| DF-016 | Buket Krisan Putih | Buket fresh | 165.000 | 1.5 | 1 | ready | p4 |
| DF-021 | Buket Tulip Pink | Buket artificial | 135.000 | 1.5 | 1 | ready | p5 |
| DF-030 | Bunga Papan Ucapan | Bunga papan | 450.000 | 5 | 3 | preorder | p6 |
| DF-033 | Buket Putih Wrap Hitam | Buket artificial | 155.000 | 2 | 2 | ready | p7 |
| DF-035 | Buket Mini Wrap Hitam | Buket artificial | 95.000 | 1 | 1 | ready | p8 |
| DF-040 | Buket Snack Cokelat | Buket snack | 175.000 | 2 | 2 | ready | p9 |

`tampilkan_harga` = **false** untuk semuanya.

Nama file foto ada di folder `storage/app/public/produk/` — gunakan file dari zip `Foto_untuk_Figma_Deepsea` (p1 … p9, hero, detail).

## produk_bahan (komposisi)

Cukup isi untuk model terlaris. Produk yang tidak disebut di bawah sengaja dibiarkan tanpa komposisi, supaya jalur "produk tanpa komposisi" ikut teruji.

**DF-008 Buket Mawar Putih Medium**
- Mawar putih 12 tangkai
- Baby's breath 1 ikat
- Kertas wrapping 2 lembar
- Pita satin 1 meter
- Kartu ucapan 1 pcs

**DF-001 Buket Lily Pink**
- Lily pink 5 tangkai
- Mawar putih 6 tangkai
- Kertas wrapping 2 lembar
- Pita satin 1 meter

**DF-016 Buket Krisan Putih**
- Krisan putih 15 tangkai
- Eucalyptus 1 ikat
- Kertas kraft cokelat 2 lembar
- Tali rami 1 meter

**DF-021 Buket Tulip Pink**
- Tulip artificial pink 12 tangkai
- Kertas wrapping 2 lembar
- Pita satin 1 meter

**DF-014 Buket Mawar Biru Navy**
- Mawar artificial biru navy 20 tangkai
- Baby's breath 1 ikat
- Kertas wrapping 3 lembar
- Pita satin 1,5 meter

**DF-033 Buket Putih Wrap Hitam**
- Mawar artificial pink 10 tangkai
- Kertas wrapping 2 lembar
- Pita satin 1 meter

> Perhatikan: mawar putih stoknya 6, sedangkan DF-008 butuh 12. Ini **disengaja** supaya fitur cek kelayakan langsung terlihat saat demo.

## pelanggan & pesanan

Tanggal acuan demo: **Sabtu, 19 September 2026**.

| kode | pelanggan | sumber | model | tanggal_jadi | status | bayar | total |
|---|---|---|---|---|---|---|---|
| DF-2609-039 | Iwan Setiawan | wa | Buket Krisan Putih | 24 Sep | dikerjakan | lunas | 165.000 |
| DF-2609-040 | Ujang Suherman | wa | Bunga Papan Ucapan × 2 | 22 Sep | masuk | dp | 900.000 |
| DF-2609-041 | Neneng Sulastri | wa | Buket Mawar Biru Navy | 19 Sep | dikerjakan | dp | 225.000 |
| DF-2609-042 | Dedi Supriatna | ig | Buket Lily Pink | 20 Sep | dikerjakan | lunas | 185.000 |
| DF-2609-043 | Rina Marlina | wa | Buket Snack Cokelat | 23 Sep | jadi | dp | 195.000 |
| DF-2609-044 | Euis Komariah | ig | Bunga Papan Ucapan | 22 Sep | masuk | dp | 450.000 |
| DF-2609-045 | Asep Kurniawan | tiktok | Bunga Papan Ucapan | 19 Sep | masuk | dp | 450.000 |
| DF-2609-046 | Yuyun Yuningsih | wa | Buket Tulip Pink | 21 Sep | masuk | dp | 135.000 |
| DF-2609-047 | Hendra Gunawan | ig | Buket Mawar Putih Medium | 22 Sep | masuk | dp | 158.000 |
| DF-2609-048 | Wulan Purnamasari | wa | Buket Snack Cokelat | 22 Sep | masuk | dp | 175.000 |
| DF-2609-038 | Lilis Suryani | wa | Buket Putih Wrap Hitam | 14 Sep | batal | dp hangus | 155.000 |

Catatan tambahan per pesanan (masuk ke `pesanan.catatan` atau `pesanan_item.kartu_ucapan`):
- DF-2609-041: "Wisuda Polsub", kartu ucapan "Selamat wisuda, Teh. Bangga pisan."
- DF-2609-042: "Ulang tahun istri"
- DF-2609-043: diantar ke Pagaden, ongkir 20.000
- DF-2609-040: "Dua papan untuk pernikahan di Kalijati", diantar
- DF-2609-044: "Syukuran kantor desa", diantar
- DF-2609-045: "Pembukaan toko", diantar

DP = 50% dari total, tanggal pembayaran = tanggal pesan.

## stok_produk_jadi

| nama | asal | pesanan | tanggal | status |
|---|---|---|---|---|
| Buket Putih Wrap Hitam | batal | DF-2609-038 | 14 Sep 2026 | tersedia |
| Buket Gerbera Oranye | produksi | — | 17 Sep 2026 | tersedia |

## pengeluaran

| tanggal | kategori | nominal | keterangan |
|---|---|---|---|
| 12 Sep 2026 | bahan | 850.000 | Kulakan bunga fresh Lembang |
| 5 Sep 2026 | bahan | 420.000 | Belanja kertas, pita, floral foam |
| 1 Sep 2026 | sewa | 700.000 | Sewa kios September |
| 1 Sep 2026 | gaji | 3.600.000 | Gaji 3 karyawan September |
| 8 Sep 2026 | listrik | 150.000 | Token listrik |
| 3 Sep 2026 | wifi | 250.000 | Langganan Wi-Fi |

## pengaturan

Isi sesuai tabel di `docs/02-DATABASE.md` bagian 14.

Dengan `jumlah_perakit = 3` dan `jam_kerja_per_hari = 6`, kapasitas harian = **18 jam**. Data pesanan di atas membuat **Selasa 22 September kelebihan beban** (DF-2609-040 10 jam + DF-2609-044 5 jam + DF-2609-047 2 jam + DF-2609-048 2 jam = 19 jam) — memang disengaja supaya peringatan kapasitas langsung kelihatan saat demo.

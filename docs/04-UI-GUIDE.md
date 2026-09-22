# Panduan Tampilan

Sumber rancangan: file Figma **Deepsea Florist — Mockup UI**
`https://www.figma.com/design/0Dc29IMXLSo12dMAYyVB3a` (8 frame)

Prinsipnya: **satu brand, dua wajah.** Halaman publik hangat dan lega, foto jadi bintangnya. Panel admin padat dan netral, dipakai untuk kerja cepat. Ibarat etalase toko vs meja kerja di belakang.

---

## 1. Token warna

Taruh di `resources/css/app.css` (atau `<style>` di layout) sebagai CSS variable. **Jangan tulis hex langsung di Blade.**

```css
:root{
  --bg:#FBF8F7;        /* latar halaman, putih hangat */
  --card:#FFFFFF;
  --ink:#2B2226;       /* teks utama,     kontras 14.6:1 */
  --ink-2:#6B5C61;     /* teks sekunder,  kontras 5.97:1 */
  --ink-3:#8F8087;     /* teks redup */
  --line:#E9DEDB;      /* pembatas */
  --line-2:#F3ECEA;    /* pembatas halus */

  --rose:#B23A57;      /* aksi/tombol utama, 5.78:1 di atas putih */
  --rose-d:#8E2B44;    /* saat ditekan */
  --rose-l:#F0A0B0;    /* DEKORASI SAJA — kontras 1.9:1, jangan untuk teks */
  --rose-wash:#FCEFF2;

  --green:#3F6B47;     --green-wash:#EDF4EE;   /* aman / selesai */
  --amber:#9A6414;     --amber-wash:#FBF2E4;   /* perhatian / menipis */
  --red:#A32B2B;       --red-wash:#FBEDED;     /* bahaya / habis / batal */

  --r:6px;             /* radius standar */
}
```

Rose diambil dari logo mitra lalu dituakan supaya lolos WCAG AA. Hijau diambil dari daun pada foto produknya sendiri. Semua angka kontras di atas sudah diukur — jangan diganti tanpa mengukur ulang.

## 2. Tipografi

| Peran | Font | Dipakai di |
|---|---|---|
| Display | **Fraunces** SemiBold | Judul halaman publik, angka besar di tile dashboard |
| Teks & UI | **Source Sans 3** | Seluruh isi, dan **seluruh** panel admin |

- Di panel admin tidak ada serif sama sekali.
- Angka dalam kolom pakai `font-variant-numeric: tabular-nums`.
- Muat lewat Google Fonts, sertakan fallback stack.

## 3. Aturan visual

- Satu tombol utama per layar. Sisanya bergaya garis (`ghost`) atau polos.
- Pembatas memakai garis tipis `--line`. Bayangan hanya untuk elemen yang benar-benar mengambang (modal, dropdown).
- Radius 6px, dan hanya pada elemen yang memang objek terpisah. Jangan semua dibulatkan.
- **Status selalu warna + tulisan**, tidak pernah warna saja. Badge punya titik bulat kecil + label.
- Ikon: gambar garis sederhana (SVG inline). **Tidak boleh emoji sebagai ikon.**
- Seluruh tulisan antarmuka berbahasa Indonesia. Tidak ada "Get Started", "Submit", "Dashboard Overview".

### Yang dihindari (ciri desain generik)
Gradien ungu-biru, glassmorphism, emoji ikon, semua sudut membulat besar, bayangan di mana-mana, data contoh kosong ("Produk 1", "Lorem ipsum"), judul berbahasa Inggris.

## 4. Komponen Blade yang perlu dibuat

| Komponen | Props | Catatan |
|---|---|---|
| `<x-pill>` | `warna` (neutral/rose/hijau/amber/merah), `label` | Badge status, ada titik bulat |
| `<x-tile>` | `label`, `nilai`, `sub`, `nada` | Angka ringkas di dashboard |
| `<x-panel>` | `judul`, `tautan` | Kotak putih dengan header bergaris |
| `<x-btn>` | `varian` (primer/garis/polos), `href` | |
| `<x-field>` | `label`, `name`, `hint` | Label + input + pesan error |
| `<x-alert>` | `nada` (bad/warn/ok), `judul` | Peringatan bahan & kapasitas |
| `<x-kartu-produk>` | `produk` | Foto 4:5, nama, kategori, badge, "Chat untuk harga" |

## 5. Delapan layar

### 5.1 Beranda
Header (logo, nav, tombol WhatsApp) · Hero dua kolom: kiri teks + dua tombol + tiga angka (2023 / 6 jenis / 1–3 hari), kanan foto · Chip kategori · "Paling sering dipesan" 4 kartu · "Cara memesan" 3 langkah bernomor · Footer 4 kolom.

Penomoran 1-2-3 dipakai karena langkahnya memang berurutan, bukan hiasan.

### 5.2 Katalog
Chip filter kategori · teks "Menampilkan X dari Y model" · grid kartu (3 kolom di desktop, 1 di HP). Badge diambil dari hasil cek kelayakan: bahan cukup → `Ready`, produk `preorder` → `Pre-order`, komposisi tidak cukup → `Bahan habis`.

### 5.3 Detail produk
Foto besar kiri · kanan: kategori, nama, badge + kode model, deskripsi, daftar spesifikasi (Kategori, Ukuran standar, Perkiraan pengerjaan, Ukuran khusus, Harga → "Dibicarakan lewat chat"), tombol WhatsApp besar, **kotak pratinjau pesan otomatis**, tiga thumbnail model serupa.

Pesan WhatsApp otomatis:
```
Halo Deepsea Florist, saya mau tanya {nama produk} ({kode}) yang ada di website. Untuk tanggal …
```
di-encode ke `https://wa.me/{no_wa}?text=...`

### 5.4 Dashboard admin
Sapaan + tanggal + tombol Tambah pesanan · **4 tile**: Pesanan aktif, Jatuh tempo hari ini (nada amber), Beban hari ini (`14/18`), Bahan menipis (nada merah) · kolom kiri: Antrian hari ini + Siap dijual langsung · kolom kanan: Beban 7 hari ke depan (batang berwarna + angka jam) + Bahan menipis.

Batang beban: hijau < 85%, amber 85–100%, merah > 100%. **Selalu ditemani angka jamnya.**

Tidak ada grafik penjualan di dashboard — itu untuk melihat ke belakang, dashboard untuk mengerjakan hari ini. Grafik ada di halaman Laporan.

### 5.5 Pesanan baru
Dua kolom. **Kiri = isian manusia**, kanan = jawaban sistem.

Kiri: nama pemesan, kontak (ditulis "boleh kosong"), sumber pesanan, model, ukuran, warna, kartu ucapan, tanggal jadi, diambil/diantar, tombol Simpan + Batal.

Kanan, berurutan: peringatan bahan kurang (merah) → panel "Model lain yang bahannya ada" dengan thumbnail + tombol Pakai → peringatan tanggal padat (amber) → kotak Perkiraan harga yang **memperlihatkan hitungannya** (modal bahan, margin, ongkos jasa, saran harga) + catatan "Ini saran, bukan harga mati."

Kalau angka harga muncul begitu saja tanpa rincian, pemilik tidak akan percaya dan akan mengabaikannya.

### 5.6 Daftar pesanan
Tab status · tabel: Kode, Pemesan, Model (+ catatan kecil di bawahnya), Tanggal jadi, Status, Pembayaran, Total.

Tanggal ditulis relatif: **"Hari ini"** (merah), **"Besok"** (amber), selebihnya "Sen, 21 Sep". Status pengerjaan dan status pembayaran **dipisah** — dua hal berbeda.

### 5.7 Bahan & stok
Tab jenis · tabel: Bahan, Jenis, Sisa, Minimum, Status, Masuk terakhir, Catatan umur. Satuan ikut ditulis di angkanya ("36 tangkai"), karena satuannya bercampur. Kolom umur hanya berisi untuk bahan fresh; artificial ditulis "Tidak layu".

Tombol "Daftar belanja" di header — mitra belanja ke Lembang seminggu sekali dan daftar ini yang dibuka di sana.

### 5.8 Fondasi
Bukan halaman aplikasi, hanya acuan di Figma.

## 6. Responsif

Owner memakai HP dan laptop bergantian, jadi panel admin **wajib** mobile-friendly, bukan sekadar "tidak rusak":

- Sidebar jadi baris menu horizontal yang bisa digeser di bawah 900px
- Kolom dua jadi satu kolom
- Tabel dibungkus `.table-responsive`
- Target sentuh minimal 40px

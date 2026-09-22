# Panduan SEO

Target: muncul di pencarian Google untuk orang yang mencari bunga di Subang.

Dokumen ini punya dua bagian:
- **Bagian A** untuk Claude Code — hal teknis yang dikerjakan di dalam sistem
- **Bagian B** untuk pemilik toko — hal yang tidak butuh coding sama sekali, tapi justru paling besar pengaruhnya

---

## 0. Kondisi nyata dan harapan yang masuk akal

Hasil pengecekan halaman 1 Google untuk "toko bunga subang" (September 2026):

| Kelompok | Contoh | Bisa dilawan? |
|---|---|---|
| Toko bunga luar kota yang menembak kata kunci Subang | Outerbloom, Millen Florist, Rosse Florist, Ratu Florist, Athaya | Berat — domainnya sudah kuat bertahun-tahun |
| Media sosial | Instagram Sukarahayu Florist, Le Florist Subang, Facebook, TikTok | Bisa |
| **Website toko bunga Subang asli** | **tidak ada satu pun** | Ini celahnya |

**Tidak ada yang bisa menjamin peringkat 1**, termasuk agensi SEO berbayar. Yang bisa dijamin adalah pengerjaan teknis yang benar dan strategi yang masuk akal. Jangan menjanjikan peringkat kepada dosen maupun mitra.

Urutan prioritas, dari yang paling besar pengaruhnya:

1. **Google Bisnis Profil** (Bagian B) — gratis, bisa selesai dalam sehari, dan ini yang memunculkan toko di kotak peta paling atas
2. **Kata kunci ekor panjang** — "buket wisuda Subang", "bunga papan Subang" — saingannya sepi
3. **SEO teknis di website** (Bagian A) — sekali kerja, wajib benar
4. **Kata kunci utama "toko bunga subang"** — target jangka panjang, 6 bulan ke atas

---

# BAGIAN A — Dikerjakan di sistem

## A1. Perubahan database

Tambahkan kolom berikut. Ini melengkapi `docs/02-DATABASE.md`, bukan menggantikannya.

**Tabel `pengaturan`** (tambahan):

| Kolom | Tipe | Nilai awal |
|---|---|---|
| meta_judul_default | varchar | Deepsea Florist — Toko Bunga & Buket di Subang |
| meta_deskripsi_default | text | Toko bunga di Cibogo, Subang. Buket wisuda, buket fresh dan artificial, buket uang, buket snack, dan bunga papan. Pesan lewat WhatsApp. |
| latitude | decimal(10,7) | koordinat kios, diisi dari Google Maps |
| longitude | decimal(10,7) | koordinat kios |
| google_maps_url | varchar nullable | link Google Bisnis Profil |

**Tabel `produk`** (tambahan):

| Kolom | Tipe | Keterangan |
|---|---|---|
| meta_deskripsi | varchar(160) nullable | kalau kosong, ambil 155 karakter pertama dari `deskripsi` |

**Tabel `kategori`** (tambahan):

| Kolom | Tipe | Keterangan |
|---|---|---|
| deskripsi | text nullable | paragraf pengantar di halaman kategori — ini yang dibaca Google |
| meta_deskripsi | varchar(160) nullable | |

> Semua kolom di atas diisi owner lewat panel admin. Jangan ditulis langsung di Blade.

## A2. Komponen `<x-seo>`

Buat satu komponen Blade yang dipakai semua halaman publik:

```blade
{{-- resources/views/components/seo.blade.php --}}
@props(['judul' => null, 'deskripsi' => null, 'gambar' => null, 'tipe' => 'website'])

<title>{{ $judul ?? $pengaturan->meta_judul_default }}</title>
<meta name="description" content="{{ $deskripsi ?? $pengaturan->meta_deskripsi_default }}">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="{{ $tipe }}">
<meta property="og:title" content="{{ $judul ?? $pengaturan->meta_judul_default }}">
<meta property="og:description" content="{{ $deskripsi ?? $pengaturan->meta_deskripsi_default }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $gambar ?? asset('img/og-default.jpg') }}">
<meta property="og:locale" content="id_ID">
<meta name="twitter:card" content="summary_large_image">
```

`$pengaturan` disediakan lewat **View Composer** supaya tersedia di semua view publik tanpa perlu dikirim dari tiap controller.

## A3. Pola judul halaman

| Halaman | Format `<title>` | Contoh |
|---|---|---|
| Beranda | `{nama_toko} — Toko Bunga & Buket di Subang` | Deepsea Florist — Toko Bunga & Buket di Subang |
| Katalog | `Katalog Buket Bunga di Subang — {nama_toko}` | |
| Kategori | `{nama kategori} di Subang — {nama_toko}` | Buket Fresh di Subang — Deepsea Florist |
| Detail produk | `{nama produk} — Buket Bunga Subang \| {nama_toko}` | Buket Mawar Biru Navy — Buket Bunga Subang \| Deepsea Florist |
| Tentang | `Tentang {nama_toko} — Toko Bunga Cibogo, Subang` | |

Aturan: panjang judul 50–60 karakter, kata kunci di depan, nama toko di belakang. Jangan menumpuk kata kunci ("toko bunga subang murah terbaik terpercaya subang") — itu justru menurunkan peringkat.

## A4. Data terstruktur (Schema.org JSON-LD)

Ini yang membuat Google paham bahwa ini toko fisik di Subang, bukan toko online dari kota lain. **Bagian paling penting di Bagian A.**

**Di layout publik**, sekali saja:

```blade
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'Florist',
  'name' => $pengaturan->nama_toko,
  'image' => asset('img/logo.png'),
  'url' => url('/'),
  'telephone' => '+62' . ltrim($pengaturan->no_wa, '0'),
  'address' => [
    '@type' => 'PostalAddress',
    'streetAddress' => 'Jl. Raya Cinangsi RT.01/RW.01, Cinangsi',
    'addressLocality' => 'Cibogo',
    'addressRegion' => 'Jawa Barat',
    'postalCode' => '41211',
    'addressCountry' => 'ID',
  ],
  'geo' => [
    '@type' => 'GeoCoordinates',
    'latitude' => $pengaturan->latitude,
    'longitude' => $pengaturan->longitude,
  ],
  'openingHours' => 'Mo-Sa 08:00-17:00',
  'priceRange' => 'Rp',
  'areaServed' => ['Subang', 'Cibogo', 'Pagaden', 'Pamanukan'],
  'sameAs' => array_filter([$pengaturan->link_ig, $pengaturan->link_tiktok]),
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
```

`Florist` adalah tipe resmi Schema.org turunan `LocalBusiness` — lebih spesifik, lebih bagus.

**Di halaman detail produk**, tambahkan:

```php
'@type' => 'Product',
'name' => $produk->nama,
'sku' => $produk->kode,
'image' => asset('storage/' . $produk->foto_utama),
'description' => $produk->deskripsi,
'brand' => ['@type' => 'Brand', 'name' => $pengaturan->nama_toko],
```

> **Jangan** menambahkan properti `offers` dengan harga, karena harga memang tidak dipublikasikan. Menulis harga palsu di data terstruktur bisa kena penalti.

Verifikasi hasilnya dengan Rich Results Test milik Google setelah live.

## A5. URL

Spec kita sudah memakai slug, jadi tinggal dipastikan:

```
/                              beranda
/katalog                       semua produk
/katalog/kategori/buket-fresh  halaman kategori
/katalog/buket-mawar-biru-navy detail produk
/tentang
/kontak
```

Aturan: huruf kecil semua, pakai tanda hubung, tanpa ID angka, tanpa query string. URL tidak boleh berubah setelah live — kalau terpaksa berubah, pasang redirect 301.

## A6. sitemap.xml dan robots.txt

Route `/sitemap.xml` yang dibuat langsung dari database (jangan file statis, nanti basi):

```php
Route::get('/sitemap.xml', function () {
    $produk = Produk::where('is_aktif', true)->get();
    $kategori = Kategori::all();
    return response()
        ->view('sitemap', compact('produk', 'kategori'))
        ->header('Content-Type', 'application/xml');
});
```

Isi sitemap: beranda, katalog, tiap kategori, tiap produk aktif, tentang, kontak. Sertakan `<lastmod>` dari `updated_at`.

`public/robots.txt`:

```
User-agent: *
Disallow: /admin
Disallow: /login
Allow: /

Sitemap: https://DOMAIN/sitemap.xml
```

Halaman admin **wajib** diblokir dari mesin pencari.

## A7. Gambar

- Nama file bukan `IMG_2847.jpg`, tapi `buket-mawar-biru-navy-deepsea-florist.jpg` — buat otomatis dari slug produk saat upload
- `alt` diisi otomatis dari nama produk + kategori: `alt="Buket Mawar Biru Navy, buket artificial dari Deepsea Florist Subang"`
- Simpan versi **WebP** selain aslinya; sajikan lewat `<picture>`
- Ukuran maksimal 1200px sisi terpanjang untuk katalog
- `loading="lazy"` untuk semua gambar kecuali foto hero
- Selalu tulis `width` dan `height` di tag gambar supaya layout tidak melompat

## A8. Kecepatan

Google menilai kecepatan. Yang perlu dilakukan:

- `php artisan config:cache route:cache view:cache` saat deploy
- Gambar WebP + lazy load (A7)
- Jangan memuat Bootstrap dan font dari banyak sumber — satu berkas CSS gabungan
- Font Google dimuat dengan `display=swap` dan `preconnect`
- Hindari query N+1: pakai `with()` di semua halaman katalog

## A9. Halaman kategori sebagai landing page

Ini yang paling sering dilewatkan. Halaman kategori jangan cuma grid foto — beri paragraf pengantar 100–150 kata yang ditulis owner lewat admin (kolom `kategori.deskripsi`).

Contoh untuk kategori Buket Fresh:

> Buket bunga segar untuk wisuda, ulang tahun, dan momen spesial lain di Subang dan sekitarnya. Bunga didatangkan langsung dari Lembang setiap minggu, jadi selalu segar saat dirangkai. Kami melayani pengambilan di kios Cinangsi, Cibogo, maupun pengantaran ke wilayah Subang kota, Pagaden, dan sekitarnya.

Kata kuncinya masuk secara wajar, bukan ditumpuk.

## A10. Pemetaan kata kunci per halaman

| Halaman | Kata kunci utama | Kata kunci pendukung |
|---|---|---|
| Beranda | toko bunga Subang | florist Subang, toko bunga Cibogo |
| Katalog | buket bunga Subang | katalog buket Subang |
| Kategori buket fresh | buket bunga segar Subang | bunga fresh Subang |
| Kategori buket artificial | buket bunga artificial Subang | buket bunga palsu Subang |
| Kategori bunga papan | bunga papan Subang | karangan bunga Subang, papan ucapan Subang |
| Kategori buket uang | buket uang Subang | money bouquet Subang |
| Kategori buket snack | buket snack Subang | buket cokelat Subang |
| Detail produk | {nama produk} Subang | |
| Tentang | florist Cibogo Subang | toko bunga dekat Polsub |

**Target awal yang realistis** (saingan paling sepi, niat beli paling tinggi):
`buket wisuda Subang` · `bunga papan Subang` · `toko bunga Cibogo` · `buket wisuda Polsub` · `buket uang Subang`

## A11. Yang tidak boleh dilakukan

| Larangan | Kenapa |
|---|---|
| Menumpuk kata kunci di footer atau teks tersembunyi | Melanggar pedoman Google, bisa kena penalti |
| Membeli backlink | Sama, dan sia-sia untuk toko lokal |
| Menyalin deskripsi produk dari toko bunga lain | Konten duplikat, tidak akan diindeks |
| Menulis harga di data terstruktur padahal harga tidak dipublikasi | Data terstruktur harus cocok dengan isi halaman |
| Mengganti URL produk setelah live tanpa redirect | Peringkat yang sudah didapat hilang |
| Mengindeks halaman admin | Membocorkan struktur sistem |

## A12. Ceklis penerimaan SEO

Tambahkan ke `docs/07-ACCEPTANCE.md`:

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

---

# BAGIAN B — Dikerjakan pemilik toko, tanpa coding

Bagian ini **lebih besar pengaruhnya** daripada seluruh Bagian A untuk kata kunci lokal. Serahkan ke Bu Siti saat sosialisasi, dampingi pengerjaannya.

## B1. Google Bisnis Profil — kerjakan paling pertama

Untuk pencarian lokal, yang muncul paling atas di Google adalah kotak peta berisi tiga toko. Isi kotak itu **tidak ditentukan website**, tapi oleh Google Bisnis Profil. Daftarnya gratis di `business.google.com`.

Langkahnya:

1. Daftar dengan akun Google milik toko (jangan akun pribadi mahasiswa)
2. Nama usaha: **Deepsea Florist Home Store** — tulis persis sama dengan yang di website
3. Kategori utama: **Toko bunga**. Kategori tambahan: Toko hadiah, Jasa dekorasi
4. Alamat lengkap + geser pin peta tepat di lokasi kios
5. Jam buka Senin–Sabtu 08.00–17.00
6. Nomor WhatsApp dan alamat website
7. Verifikasi — biasanya lewat kartu pos atau video, butuh beberapa hari
8. Upload minimal 10 foto: kios tampak depan, proses merangkai, dan produk
9. Aktifkan tombol pesan

**Yang menentukan peringkat di kotak peta**, berurutan: jarak pencari ke toko, kelengkapan profil, **jumlah dan kualitas ulasan**, serta keaktifan.

## B2. Ulasan pelanggan

Ini faktor terkuat yang bisa dikendalikan sendiri.

- Setelah pesanan diambil, kirim pesan WA: *"Terima kasih sudah pesan di Deepsea Florist. Kalau berkenan, boleh bantu kasih ulasan di Google? Linknya ada di sini 🙏"* — (link pendek ulasan tersedia di dalam Google Bisnis Profil)
- Target awal **10 ulasan** dalam dua bulan pertama
- **Balas semua ulasan**, termasuk yang jelek. Balasan yang sopan ke ulasan jelek justru menaikkan kepercayaan
- **Jangan** membeli ulasan atau membuat akun palsu. Google mendeteksi pola ini dan bisa menghapus seluruh listing

## B3. Konsistensi nama, alamat, nomor telepon

Tulis **persis sama** di semua tempat: website, Google Bisnis Profil, Instagram, TikTok, Facebook.

Kalau di Google tertulis "Jl. Raya Cinangsi RT.01/RW.01" sedangkan di Instagram "Jalan Raya Cinangsi", Google jadi ragu ini toko yang sama. Buat satu catatan berisi teks resmi nama-alamat-telepon, lalu salin dari situ setiap kali mengisi profil baru.

> Catatan khusus: akun Instagram saat ini masih `marya_florist` dan akan diganti menjadi Deepsea Florist. **Ganti dulu** sebelum mendaftarkan profil-profil lain, supaya tidak perlu mengubah dua kali.

## B4. Konten rutin

Ini yang bikin bertahan di peringkat, dan ini pekerjaan tanpa akhir:

- Posting Google Bisnis Profil 1× seminggu (foto produk baru + satu kalimat)
- Tambah produk baru ke katalog website secara berkala — website yang isinya tidak pernah berubah akan turun sendiri
- Unggah foto ke profil Google, bukan hanya ke Instagram

## B5. Daftar ke direktori lokal

Gratis, dan membantu Google memastikan toko ini nyata:

- Google Bisnis Profil (B1)
- Apple Maps / Apple Business Connect
- Bing Places
- Facebook Page
- Instagram dengan alamat di bio

## B6. Harapan waktu

| Waktu | Yang wajar terjadi |
|---|---|
| Minggu 1–2 | Profil Google terverifikasi, mulai muncul di peta saat dicari dengan nama tokonya |
| Bulan 1–2 | Website terindeks, muncul untuk pencarian nama "Deepsea Florist" |
| Bulan 2–4 | Mulai muncul di kata kunci ekor panjang: "buket wisuda Subang", "bunga papan Subang" |
| Bulan 4–6 | Berpeluang masuk kotak peta untuk "toko bunga Subang" bila ulasan sudah cukup |
| Bulan 6+ | Bersaing di hasil organik "toko bunga subang" — **tidak dijamin** |

Kalau tidak ada yang mengurus setelah kelompok selesai kuliah, semua ini berhenti. Bicarakan sejak awal siapa yang melanjutkan.

## B7. Cara mengukur

Pasang dua alat gratis, keduanya butuh akses ke domain:

- **Google Search Console** — memperlihatkan kata kunci apa yang membawa orang ke website, dan halaman mana yang sudah terindeks. Daftarkan segera setelah website live, lalu kirim `sitemap.xml` di sana.
- **Statistik Google Bisnis Profil** — memperlihatkan berapa orang melihat profil, menekan tombol arah, dan menghubungi WhatsApp.

Jangan menilai keberhasilan dari peringkat saja. Yang penting: **berapa chat WhatsApp masuk yang berasal dari Google.** Itu angka yang berarti untuk Bu Siti.

---

## Catatan penutup: siapa pemilik domain

Domain dan hosting saat ini ditanggung mahasiswa. Seluruh hasil SEO menempel pada domain — kalau domain kedaluwarsa atau berpindah, semuanya hilang dan mitra kembali dari nol.

**Saran: domain didaftarkan atas nama pemilik toko sejak awal**, dan mahasiswa yang mengelola. Ini yang membedakan project pendampingan yang bertahan dengan yang mati setelah mahasiswanya lulus.

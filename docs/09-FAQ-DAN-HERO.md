# Halaman FAQ dan Latar Halaman Tentang

Pelengkap `docs/08-SEO.md`. Dua pekerjaan: menambah halaman FAQ dan memberi latar foto pada halaman Tentang.

---

# BAGIAN 1 — Halaman FAQ

## 1.1 Harapan yang jujur soal FAQ dan SEO

Sejak Mei 2026, **Google menghapus tampilan khusus FAQ di hasil pencarian**. Dulu FAQ bisa muncul sebagai daftar pertanyaan yang bisa dibuka langsung di halaman hasil; sekarang tidak lagi untuk situs biasa.

Jadi FAQ **bukan** jalan pintas supaya tampil menonjol di Google. Nilainya ada di tempat lain, dan tetap nyata:

| Manfaat | Penjelasan |
|---|---|
| Menjawab pencarian berbentuk pertanyaan | Orang mengetik "berapa lama buket bunga fresh bertahan" atau "harga buket wisuda Subang". Halaman FAQ menjawabnya langsung |
| Menambah isi halaman yang relevan | Website katalog isinya foto dan nama produk — sedikit teks. FAQ menambah teks yang benar-benar dicari orang |
| Dipakai asisten AI | Ringkasan AI di mesin pencari dan chatbot mengambil jawaban dari halaman yang menjawab pertanyaan dengan jelas |
| Mengurangi pertanyaan berulang di WhatsApp | Manfaat langsung untuk pemilik, bukan cuma untuk Google |

Yang terakhir itu sering dilupakan: pemilik menjawab pertanyaan yang sama setiap hari di WhatsApp. FAQ mengurangi itu.

Data terstruktur `FAQPage` tetap dipasang — tidak merugikan dan murah — tetapi **jangan menjanjikan tampilan khusus di hasil pencarian.**

## 1.2 Tabel baru: `faq`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| pertanyaan | varchar | |
| jawaban | text | |
| urutan | int | default 0 |
| is_aktif | boolean | default true |
| timestamps | | |

Dikelola owner lewat panel admin (CRUD sederhana, khusus role `owner`). **Jangan hardcode isi FAQ di Blade** — owner harus bisa menambah pertanyaan sendiri ketika ada pertanyaan baru yang sering masuk.

## 1.3 Halaman

- Route: `Route::get('/faq', FaqController::class)->name('publik.faq')`
- View: `resources/views/publik/faq.blade.php`
- Judul halaman: `Pertanyaan Seputar Buket dan Bunga — {nama_toko}`
- Tautan: dari footer kolom "Jelajahi", dari halaman `/kontak`, dan dari bagian bawah `/tentang`. **Tidak perlu masuk menu utama** — menu sudah empat item.
- Tambahkan ke `sitemap.blade.php`.

## 1.4 Bentuk tampilan

Accordion seperti contoh: pertanyaan sebagai baris yang bisa diklik, tanda panah di kanan, jawaban terbuka di bawahnya.

**Aturan penting:** jawaban harus **sudah ada di HTML sejak halaman dimuat**, hanya disembunyikan dengan CSS. Jangan dimuat lewat JavaScript setelah diklik — mesin pencari tidak akan membacanya.

Paling aman dan paling sederhana: pakai `<details>` dan `<summary>` bawaan HTML. Tidak butuh JavaScript sama sekali, sudah bisa diakses keyboard, dan isinya tetap terbaca Google.

```blade
@foreach ($faq as $item)
    <details class="faq-item">
        <summary>
            <h2 class="h6 mb-0">{{ $item->pertanyaan }}</h2>
            <x-ikon-panah />
        </summary>
        <div class="faq-jawaban">{!! nl2br(e($item->jawaban)) !!}</div>
    </details>
@endforeach
```

Gaya visual mengikuti `docs/04-UI-GUIDE.md`: latar `--card`, garis `--line`, radius 6px, panah berputar saat terbuka, tanpa bayangan.

## 1.5 Data terstruktur

Di halaman FAQ saja, bukan di layout:

```php
'@context' => 'https://schema.org',
'@type' => 'FAQPage',
'mainEntity' => $faq->map(fn ($f) => [
    '@type' => 'Question',
    'name' => $f->pertanyaan,
    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->jawaban],
]),
```

Isi schema **wajib sama persis** dengan yang terlihat di halaman. Jangan menaruh pertanyaan di schema yang tidak ada di halaman.

## 1.6 Isi FAQ untuk seeder

Dua belas pertanyaan berikut dipilih dari pertanyaan yang benar-benar diketik orang, dan dari aturan bisnis mitra yang sudah dikunci. Masukkan ke `FaqSeeder`.

**1. Berapa lama buket bunga fresh bisa bertahan?**
Buket bunga segar biasanya bertahan 5 sampai 7 hari kalau dirawat dengan benar. Simpan di tempat sejuk, jauh dari sinar matahari langsung dan angin kipas. Kalau memakai floral foam, siram sedikit air setiap hari. Untuk acara yang jauh hari, buket artificial lebih aman karena tidak layu.

**2. Apa bedanya buket bunga fresh dan buket artificial?**
Buket fresh memakai bunga hidup seperti mawar, lily, krisan, dan baby's breath — wanginya nyata dan tampilannya alami, tetapi umurnya terbatas. Buket artificial memakai bunga kain atau plastik berkualitas yang bisa disimpan bertahun-tahun sebagai kenang-kenangan. Untuk wisuda dan hadiah, keduanya sama-sama sering dipesan.

**3. Berapa lama pengerjaan buket, dan kapan sebaiknya pesan?**
Sebagian besar buket selesai dalam 1 sampai 3 hari. Untuk bunga papan biasanya perlu waktu lebih lama. Saat musim wisuda pesanan sangat ramai, jadi sebaiknya pesan paling lambat 3 hari sebelum tanggal dibutuhkan. Pesanan mendadak tetap bisa dicoba — tanyakan lewat WhatsApp, kami akan cek jadwal pengerjaan hari itu.

**4. Berapa harga buket bunga di Subang?**
Harga menyesuaikan jenis bunga, ukuran, dan tingkat kerumitan rangkaian. Karena itu harga tidak kami pasang di katalog. Pilih modelnya di katalog, lalu kirim kode modelnya lewat WhatsApp — kami akan langsung memberi harga untuk ukuran yang diinginkan.

**5. Apakah melayani buket wisuda untuk mahasiswa Polsub?**
Ya. Buket wisuda adalah pesanan yang paling sering kami kerjakan, termasuk untuk wisuda Politeknik Negeri Subang dan sekolah di sekitar Subang. Warna buket bisa disesuaikan dengan warna toga atau almamater. Pesan lebih awal karena musim wisuda selalu penuh.

**6. Apakah bisa diantar? Sampai daerah mana?**
Sebagian besar pesanan diambil langsung di kios kami di Cinangsi, Cibogo. Pengantaran bisa dilakukan ke Subang kota, Pagaden, dan sekitarnya. Ongkos kirim menyesuaikan jarak dan disepakati lewat chat sebelum pengerjaan dimulai.

**7. Bagaimana cara memesan bunga papan ucapan?**
Kirim lewat WhatsApp: teks ucapan lengkap, nama pengirim, tanggal dan jam dipasang, serta alamat lokasinya. Bunga papan perlu waktu pengerjaan lebih lama dan pemasangan di tempat, jadi sebaiknya dipesan beberapa hari sebelumnya.

**8. Apakah bisa minta warna dan ukuran khusus?**
Bisa. Ukuran di katalog adalah ukuran standar. Ukuran yang lebih besar atau permintaan warna tertentu tetap kami layani dengan penyesuaian harga. Kalau bunga yang diminta sedang tidak tersedia, kami akan menawarkan model lain dengan nuansa warna yang mirip.

**9. Bagaimana cara pembayarannya?**
Pemesanan dimulai dengan DP 50 persen, sisanya dilunasi setelah buket jadi dan sebelum diambil atau diantar. Pembayaran bisa lewat transfer bank, QRIS, atau tunai di tempat.

**10. Bunga segarnya dari mana?**
Bunga segar kami ambil langsung dari Lembang setiap minggu, jadi stoknya selalu baru saat dirangkai. Karena dikulak mingguan, ketersediaan jenis bunga tertentu bisa berbeda-beda. Tanyakan dulu lewat WhatsApp kalau menginginkan bunga tertentu.

**11. Apa itu buket uang dan buket snack?**
Buket uang adalah rangkaian berisi lembaran uang yang dilipat menjadi bentuk bunga — uangnya disiapkan pemesan, kami mengerjakan lipatan dan rangkaiannya. Buket snack berisi cokelat atau makanan ringan yang dirangkai seperti buket bunga. Keduanya populer untuk hadiah wisuda dan ulang tahun.

**12. Bagaimana merawat buket supaya lebih awet?**
Untuk buket fresh: jauhkan dari sinar matahari langsung, angin kipas, dan AC yang mengarah langsung; potong sedikit ujung tangkai kalau dipindah ke vas; ganti airnya setiap hari. Untuk buket artificial: cukup dibersihkan debunya dengan kuas lembut dan jangan disimpan di tempat lembap.

> Semua jawaban di atas mengikuti aturan bisnis yang sudah dikunci di `CLAUDE.md` (DP 50%, pelunasan sebelum serah terima, harga tidak dipublikasikan, kulakan mingguan dari Lembang). Kalau aturan bisnisnya berubah, perbarui juga jawaban di sini.

## 1.7 Yang tidak boleh dilakukan

- Jangan membuat pertanyaan palsu yang tidak pernah ditanyakan orang hanya untuk menyelipkan kata kunci
- Jangan mengulang kata "toko bunga Subang" di setiap jawaban — cukup muncul wajar di beberapa jawaban
- Jangan menulis harga angka di jawaban, karena harga memang tidak dipublikasikan
- Jangan menaruh jawaban di dalam JavaScript

---

# BAGIAN 2 — Latar foto halaman Tentang

## 2.1 Foto yang dipakai

Gunakan file yang sudah disiapkan:

```
public/img/tentang-hero.webp          1920 × 760   (± 58 KB)
public/img/tentang-hero.jpg           1920 × 760   (cadangan)
public/img/tentang-hero-mobile.webp    900 × 620   (± 51 KB)
public/img/tentang-hero-mobile.jpg     900 × 620   (cadangan)
```

Foto diambil dari koleksi mitra sendiri: dua buket yang sedang tergeletak di meja kerja beralas taplak kotak-kotak. Dipilih karena **memperlihatkan proses kerja, bukan satu produk jadi** — persis fungsi halaman Tentang. Foto produk tunggal akan terasa seperti iklan, bukan cerita.

Watermark pada foto asli berada di bagian bawah dan **tidak ikut terpotong ke dalam bidang latar**, jadi tidak mengganggu.

## 2.2 Cara memasang

Struktur: foto sebagai latar, lapisan warna di atasnya, teks di paling atas.

```blade
<section class="tentang-hero">
    <div class="tentang-hero-isi container text-center">
        <p class="eyebrow">Sejak 2023</p>
        <h1>Tentang {{ $pengaturan->nama_toko }}</h1>
        <p class="lead">{{ ... }}</p>
        <div class="d-flex justify-content-center gap-2">...</div>
    </div>
</section>
```

```css
.tentang-hero{
  position:relative;
  padding-block:5rem;
  background-image:url('/img/tentang-hero-mobile.webp');
  background-size:cover;
  background-position:center;
}
@media (min-width:768px){
  .tentang-hero{ background-image:url('/img/tentang-hero.webp'); }
}
.tentang-hero::before{
  content:"";
  position:absolute; inset:0;
  background:var(--bg);      /* #FBF8F7 */
  opacity:.72;
}
.tentang-hero-isi{ position:relative; }   /* wajib, supaya teks di atas lapisan */
```

## 2.3 Aturan yang tidak boleh dilanggar

| Aturan | Alasan |
|---|---|
| Lapisan warna memakai `var(--bg)` dengan opacity **0.70–0.75** | Di bawah 0.70 teks mulai sulit dibaca; di atas 0.80 fotonya jadi percuma |
| Teks tetap memakai `--ink` dan `--ink-2`, jangan diubah jadi abu-abu terang | Kontras sudah diukur di `docs/04-UI-GUIDE.md` |
| Jangan memakai `background-attachment: fixed` | Patah-patah di HP dan mengganggu pembaca layar |
| Jangan menaruh teks langsung di atas foto tanpa lapisan | Kontrasnya tidak terkendali karena foto tiap bagian beda terang |
| Latar ini **hanya** untuk halaman Tentang | Kalau dipakai di semua halaman, website jadi berat dan monoton |
| Foto latar murni hiasan → pakai `background-image` di CSS, **bukan** tag `<img>` | Foto dekoratif tidak perlu dibaca pembaca layar |

## 2.4 Baris angka di bawah hero

Contoh yang dijadikan acuan memakai tiga angka besar. Untuk Deepsea, angka yang jujur dan bisa dipertanggungjawabkan:

| Angka | Label |
|---|---|
| 2023 | Mulai merangkai |
| 6 jenis | Pilihan produk |
| 1–3 hari | Waktu pengerjaan |

Ambil "6 jenis" dari `$kategori->count()`, jangan ditulis manual — kalau owner menambah kategori, angkanya ikut berubah.

**Jangan mengarang angka** seperti "500+ pelanggan puas" atau "100% kepuasan" kalau tidak ada datanya. Itu langsung terbaca palsu, dan mitra yang menanggung akibatnya.

---

## Ceklis penerimaan

Tambahkan ke `docs/07-ACCEPTANCE.md`:

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

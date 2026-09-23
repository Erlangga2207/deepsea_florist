# PWA untuk Panel Admin

Menjadikan panel admin bisa dipasang di layar utama HP pemilik, seperti aplikasi.

---

## 1. Jawaban singkat

**Bisa, dan layak dikerjakan** — tetapi hanya sebatas *installable + cache aset statis*. Perkiraan waktu: setengah hari.

Bagian PWA yang lain (offline penuh, push notification) **tidak dikerjakan**, dan alasannya ada di bagian 3. Ini bukan soal sulit, tapi soal berbahaya untuk data stok.

## 2. Yang didapat

| Manfaat | Nyata untuk mitra? |
|---|---|
| Ikon di layar utama HP, buka tanpa mengetik alamat | Ya — ini manfaat terbesar. Pemilik tinggal ketuk ikon |
| Terbuka layar penuh tanpa address bar | Ya — layar HP jadi lebih lega untuk tabel pesanan |
| Splash screen dan warna tema sendiri | Kosmetik, tapi membuat sistem terasa seperti aplikasi beneran |
| Buka kedua dan seterusnya lebih cepat | Ya — CSS, font, dan ikon diambil dari cache |
| Terlihat profesional saat sosialisasi ke mitra dan demo ke dosen | Ya |

Yang **tidak** didapat: PWA di panel admin **tidak berpengaruh sama sekali ke SEO**. Halaman admin diblokir di `robots.txt`, jadi mesin pencari memang tidak pernah melihatnya. Jangan pernah menyebut PWA sebagai alasan SEO.

## 3. Yang sengaja TIDAK dibuat

### 3.1 Input pesanan saat offline — JANGAN

Ini yang biasanya dibayangkan orang saat mendengar "PWA". Supaya form pesanan bisa diisi tanpa internet lalu dikirim belakangan, dibutuhkan IndexedDB, background sync, dan penanganan konflik data.

Masalahnya bukan sulitnya, tapi akibatnya: **stok bahan akan bentrok.** Bayangkan pemilik menginput pesanan offline di kios, karyawan menginput pesanan lain online di rumah, keduanya memakai mawar putih yang sama. Saat yang offline tersinkron, stok sudah berubah dan sistem tidak tahu harus percaya yang mana.

Sistem ini punya aturan "stok hanya berubah lewat MutasiStokService" justru supaya riwayatnya bisa dipercaya. Offline sync merusak jaminan itu.

Kalau internet mati, cara lama tetap ada: catat di kertas, input setelah internet kembali. Untuk 1–5 pesanan sehari, itu jauh lebih aman daripada data stok yang salah.

### 3.2 Push notification — belum perlu

Di iOS, push hanya jalan setelah aplikasi dipasang, dan butuh server push sendiri. Untuk dua pengguna yang tiap pagi memang membuka dashboard, ini kerja besar dengan manfaat kecil. Lewati.

### 3.3 Halaman publik ikut jadi PWA — jangan

`scope` dan `start_url` **hanya** `/admin`. Kalau scope-nya `/`, pengunjung biasa yang cuma mau lihat katalog akan ditawari memasang "aplikasi" — aneh dan mengganggu.

## 4. Bahaya utama: cache yang salah

Ini bagian yang paling mudah membuat sistem jadi berbahaya, jadi baca pelan-pelan.

Panel admin menampilkan **data yang berubah terus**: sisa stok, status pesanan, beban tanggal. Kalau halaman admin ikut di-cache dan disajikan dari cache, pemilik bisa melihat **stok basi** lalu mengambil keputusan yang salah — menerima pesanan padahal bahannya sudah habis.

Aturan yang tidak boleh dilanggar:

| Jenis berkas | Strategi | Alasan |
|---|---|---|
| CSS, JS, font, logo, ikon PWA | **cache-first** | Isinya tidak berubah; ini sumber kecepatannya |
| Semua halaman HTML `/admin/...` | **network-only**, dengan halaman cadangan bila benar-benar tidak ada koneksi | Data harus selalu baru |
| Semua request POST/PUT/DELETE | **jangan disentuh service worker sama sekali** | Jangan pernah mengantre atau mengulang kiriman data |
| Foto produk dari `/storage/...` | cache-first boleh, tetapi batasi | Foto jarang berubah |

Kalau ragu antara cepat dan benar pada panel admin, **pilih benar**.

## 5. Berkas yang perlu dibuat

```
public/manifest.json
public/sw.js
public/img/pwa/icon-192.png
public/img/pwa/icon-512.png
public/img/pwa/icon-maskable-192.png
public/img/pwa/icon-maskable-512.png
public/img/pwa/apple-touch-icon.png
resources/views/admin/offline.blade.php   (atau public/offline.html)
```

Ikon sudah disediakan di paket `PWA_Icons_Deepsea.zip` — salin ke `public/img/pwa/`.

### 5.1 manifest.json

```json
{
  "name": "Deepsea Florist — Panel Admin",
  "short_name": "Deepsea Admin",
  "description": "Pencatatan pesanan, stok bahan, dan laporan Deepsea Florist Home Store.",
  "start_url": "/admin",
  "scope": "/admin",
  "display": "standalone",
  "orientation": "portrait-primary",
  "lang": "id",
  "dir": "ltr",
  "background_color": "#FBF8F7",
  "theme_color": "#B23A57",
  "icons": [
    { "src": "/img/pwa/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/img/pwa/icon-512.png", "sizes": "512x512", "type": "image/png" },
    { "src": "/img/pwa/icon-maskable-192.png", "sizes": "192x192", "type": "image/png", "purpose": "maskable" },
    { "src": "/img/pwa/icon-maskable-512.png", "sizes": "512x512", "type": "image/png", "purpose": "maskable" }
  ],
  "shortcuts": [
    { "name": "Pesanan baru", "url": "/admin/pesanan/create" },
    { "name": "Daftar pesanan", "url": "/admin/pesanan" },
    { "name": "Bahan & stok", "url": "/admin/bahan" }
  ]
}
```

`shortcuts` muncul saat ikon ditekan lama di Android — pintasan langsung ke "Pesanan baru". Kecil, tapi persis yang dipakai pemilik setiap hari.

### 5.2 Dipasang di layout admin saja

Di `resources/views/layouts/admin.blade.php` bagian `<head>`:

```blade
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#B23A57">
<link rel="apple-touch-icon" href="/img/pwa/apple-touch-icon.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Deepsea Admin">
```

**Jangan** ditambahkan ke `layouts/publik.blade.php`.

### 5.3 Pendaftaran service worker

Di akhir layout admin:

```blade
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js', { scope: '/admin' })
      .catch(() => {});   // gagal daftar tidak boleh merusak halaman
  });
}
</script>
```

> Supaya service worker boleh memakai `scope: '/admin'` padahal berkasnya di root, kirim header `Service-Worker-Allowed: /admin` pada respons `/sw.js`. Paling mudah: layani lewat route Laravel yang menambahkan header itu, bukan sebagai file statis.

### 5.4 sw.js

Isinya sederhana saja. Jangan memakai Workbox — menambah dependensi tanpa perlu.

```js
const VERSI = 'deepsea-v1';
const ASET = [
  '/offline',
  '/css/app.css',
  '/js/app.js',
  '/img/logo.png',
  '/img/pwa/icon-192.png',
];

self.addEventListener('install', (e) => {
  e.waitUntil(caches.open(VERSI).then((c) => c.addAll(ASET)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys()
      .then((k) => Promise.all(k.filter((n) => n !== VERSI).map((n) => caches.delete(n))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (e) => {
  const req = e.request;

  // Jangan sentuh apa pun selain GET — pengiriman data tidak boleh diantre
  if (req.method !== 'GET') return;

  const url = new URL(req.url);
  if (url.origin !== location.origin) return;

  // Halaman: selalu ambil dari jaringan. Data stok tidak boleh basi.
  if (req.mode === 'navigate') {
    e.respondWith(fetch(req).catch(() => caches.match('/offline')));
    return;
  }

  // Aset statis: dari cache dulu
  if (/\.(css|js|png|jpg|jpeg|webp|svg|woff2?)$/.test(url.pathname)) {
    e.respondWith(
      caches.match(req).then((hit) => hit || fetch(req).then((res) => {
        const salinan = res.clone();
        caches.open(VERSI).then((c) => c.put(req, salinan));
        return res;
      }))
    );
  }
});
```

Naikkan `VERSI` setiap kali CSS atau JS berubah, supaya cache lama dibuang. Tulis catatan ini di `CLAUDE.md` bagian perintah deploy — kalau lupa, pemilik akan melihat tampilan lama dan bingung.

### 5.5 Halaman offline

Route `/offline` yang mengembalikan halaman statis sederhana:

> **Tidak ada koneksi internet**
> Panel admin perlu internet supaya data pesanan dan stok selalu benar.
> Catat dulu pesanannya di kertas, lalu buka lagi halaman ini setelah internet kembali.
> [Coba lagi]

Jujur dan memberi jalan keluar, bukan halaman error kosong.

## 6. Syarat yang sering terlewat

| Syarat | Catatan |
|---|---|
| **HTTPS wajib** | Service worker tidak jalan di `http://` kecuali `localhost`. Jadi di XAMPP `127.0.0.1:8000` aman, tetapi begitu naik hosting **harus** pakai SSL. Hostinger menyediakan SSL gratis — aktifkan sebelum menguji PWA |
| Pemasangan di iPhone | Tidak ada tombol otomatis. Pemilik harus membuka Safari → tombol Bagikan → "Tambahkan ke Layar Utama". Tuliskan ini di panduan untuk mitra |
| Pemasangan di Android | Chrome memunculkan tawaran sendiri, atau lewat menu → "Instal aplikasi" |
| Login tetap berlaku | Sesi Laravel tetap seperti biasa. PWA tidak mengubah autentikasi sama sekali |
| Jangan pakai `display: fullscreen` | `standalone` sudah cukup; `fullscreen` menyembunyikan jam dan baterai, mengganggu saat dipakai kerja |

## 7. Apakah ini masuk materi kuliah?

Tidak ada di silabus PBO 1. Tetapi:

- Pengerjaannya kecil (setengah hari) dan **tidak menyentuh logika sistem sama sekali** — jadi risikonya hampir nol
- Saat sosialisasi ke mitra, memasang ikon ke HP Bu Siti langsung di depan orangnya itu momen yang kuat
- Saat demo ke dosen, bisa ditunjukkan bahwa sistem ini memang dirancang untuk dipakai dari HP, bukan sekadar "responsive"

**Kerjakan paling akhir**, setelah Tahap 7 di `docs/06-BUILD-PLAN.md` selesai. Kalau waktunya habis, ini yang pertama dibuang — bukan tiga fitur andalan.

## 8. Ceklis penerimaan

Tambahkan ke `docs/07-ACCEPTANCE.md`:

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

## 9. Prompt untuk Claude Code

```
Baca docs/10-PWA-ADMIN.md, lalu kerjakan seluruh isinya.
Ikon PWA sudah ada di public/img/pwa/.
Perhatikan khusus bagian 4 soal strategi cache: halaman /admin
TIDAK BOLEH disajikan dari cache, dan service worker tidak boleh
menyentuh request non-GET.
Setelah selesai, jalankan ceklis P1-P9 di bagian 8.
```

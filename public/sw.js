// Service worker panel admin (docs/10-PWA-ADMIN.md).
// NAIKKAN VERSI setiap kali app.css atau berkas di ASET berubah, supaya cache lama dibuang.
const VERSI = 'deepsea-v1';
const FOTO = 'deepsea-foto';
const MAKS_FOTO = 60;
const ASET = [
  '/offline',
  '/css/app.css',
  '/img/logo.png',
  '/img/pwa/icon-192.png',
];

self.addEventListener('install', (e) => {
  e.waitUntil(caches.open(VERSI).then((c) => c.addAll(ASET)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys()
      .then((k) => Promise.all(k.filter((n) => n !== VERSI && n !== FOTO).map((n) => caches.delete(n))))
      .then(() => self.clients.claim())
  );
});

// Foto produk jarang berubah, tapi jumlahnya terus bertambah: simpan paling banyak MAKS_FOTO, buang yang terlama.
async function batasiFoto() {
  const c = await caches.open(FOTO);
  const kunci = await c.keys();
  await Promise.all(kunci.slice(0, Math.max(0, kunci.length - MAKS_FOTO)).map((k) => c.delete(k)));
}

function dariCache(req, namaCache, setelahSimpan) {
  return caches.match(req).then((hit) => hit || fetch(req).then((res) => {
    if (res.ok) {
      const salinan = res.clone();
      caches.open(namaCache).then((c) => c.put(req, salinan)).then(setelahSimpan);
    }
    return res;
  }));
}

self.addEventListener('fetch', (e) => {
  const req = e.request;

  // Jangan sentuh apa pun selain GET — pengiriman data tidak boleh diantre atau diulang
  if (req.method !== 'GET') return;

  const url = new URL(req.url);
  if (url.origin !== location.origin) return;

  // Halaman: selalu dari jaringan, tidak pernah disimpan. Data stok tidak boleh basi.
  if (req.mode === 'navigate') {
    e.respondWith(fetch(req).catch(() => caches.match('/offline')));
    return;
  }

  if (url.pathname.startsWith('/storage/')) {
    e.respondWith(dariCache(req, FOTO, batasiFoto));
    return;
  }

  // Aset statis: dari cache dulu
  if (/\.(css|js|png|jpg|jpeg|webp|svg|woff2?)$/.test(url.pathname)) {
    e.respondWith(dariCache(req, VERSI));
  }
});

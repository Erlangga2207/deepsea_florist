{{-- Disimpan service worker dan ditampilkan saat tidak ada koneksi. Jangan pakai data database di sini. --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#B23A57">
    <title>Tidak ada koneksi — Deepsea Florist</title>
    <link href="/css/app.css" rel="stylesheet">
</head>
<body class="offline">
    <main class="offline-kotak kotak">
        <img src="/img/logo.png" alt="" width="64" height="64">
        <h1>Tidak ada koneksi internet</h1>
        <p>Panel admin perlu internet supaya data pesanan dan stok selalu benar.</p>
        <p>Catat dulu pesanannya di kertas, lalu buka lagi halaman ini setelah internet kembali.</p>
        <a href="/admin" class="offline-tombol">Coba lagi</a>
    </main>
</body>
</html>

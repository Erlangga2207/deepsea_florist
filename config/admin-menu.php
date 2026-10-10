<?php

// Satu-satunya sumber menu panel admin: sidebar desktop dan menu HP merender dari sini.
// role: null = semua, 'owner' = khusus pemilik (sama dengan middleware 'owner' di routes/web.php).
// badge: kunci hitungan yang diisi view composer di AppServiceProvider.
// bawah: grup dipisah garis di bagian bawah sidebar.

return [
    [
        'judul' => null,
        'item' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'ikon' => 'M3 3h7v9H3zM14 3h7v5h-7zM14 12h7v9h-7zM3 16h7v5H3z'],
            ['label' => 'Pesanan', 'route' => 'admin.pesanan.index', 'ikon' => 'M9 3h6v4H9zM9 5H5v16h14V5h-4M9 12h6M9 16h4', 'badge' => 'pesanan_masuk'],
        ],
    ],
    [
        'judul' => 'Produksi',
        'item' => [
            ['label' => 'Bahan & stok', 'route' => 'admin.bahan.index', 'ikon' => 'M4 7l8-4 8 4-8 4zM4 12l8 4 8-4M4 17l8 4 8-4', 'badge' => 'bahan_menipis'],
            ['label' => 'Daftar belanja', 'route' => 'admin.daftar-belanja', 'ikon' => 'M3 4h2l2 12h11l2-8H6M9 20h.01M17 20h.01'],
            ['label' => 'Riwayat stok', 'route' => 'admin.mutasi-stok.index', 'ikon' => 'M7 4v16M3 16l4 4 4-4M17 20V4M13 8l4-4 4 4'],
            ['label' => 'Buket siap jual', 'route' => 'admin.stok-produk-jadi.index', 'ikon' => 'M3 7h18v13H3zM3 7l3-4h12l3 4M9 11h6'],
        ],
    ],
    [
        'judul' => 'Toko online',
        'item' => [
            ['label' => 'Produk', 'route' => 'admin.produk.index', 'ikon' => 'M12 21c-4-3-7-6-7-10a7 7 0 0 1 14 0c0 4-3 7-7 10zM12 8v6'],
            ['label' => 'Kategori', 'route' => 'admin.kategori.index', 'ikon' => 'M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z'],
            ['label' => 'Cabang', 'route' => 'admin.cabang.index', 'ikon' => 'M12 21s-7-6.1-7-11a7 7 0 0 1 14 0c0 4.9-7 11-7 11zM12 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z', 'role' => 'owner'],
            ['label' => 'FAQ', 'route' => 'admin.faq.index', 'ikon' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18zM9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6V14M12 17h.01', 'role' => 'owner'],
        ],
    ],
    [
        'judul' => 'Keuangan',
        'item' => [
            ['label' => 'Pengeluaran', 'route' => 'admin.pengeluaran.index', 'ikon' => 'M3 6h18v12H3zM3 10h18M7 15h3', 'role' => 'owner'],
            ['label' => 'Laporan', 'route' => 'admin.laporan.index', 'ikon' => 'M4 20V10M10 20V4M16 20v-7M22 20H2', 'role' => 'owner'],
        ],
    ],
    [
        'judul' => null,
        'bawah' => true,
        'item' => [
            ['label' => 'Pengguna', 'route' => 'admin.pengguna.index', 'ikon' => 'M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-4-5.7', 'role' => 'owner'],
            ['label' => 'Pengaturan', 'route' => 'admin.pengaturan.edit', 'ikon' => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM4 6h4M16 6h4M4 18h4M16 18h4M12 3v3M12 18v3', 'role' => 'owner'],
        ],
    ],
];

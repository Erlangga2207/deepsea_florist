@php
    // Menu baru muncul otomatis begitu route-nya dibuat di tahap berikutnya.
    // [nama route, label, path ikon SVG, khusus owner]
    $menu = [
        ['admin.dashboard', 'Dashboard', 'M3 3h7v9H3zM14 3h7v5h-7zM14 12h7v9h-7zM3 16h7v5H3z', false],
        ['admin.pesanan.index', 'Pesanan', 'M9 3h6v4H9zM9 5H5v16h14V5h-4M9 12h6M9 16h4', false],
        ['admin.produk.index', 'Produk', 'M12 21c-4-3-7-6-7-10a7 7 0 0 1 14 0c0 4-3 7-7 10zM12 8v6', false],
        ['admin.kategori.index', 'Kategori', 'M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z', false],
        ['admin.bahan.index', 'Bahan & stok', 'M4 7l8-4 8 4-8 4zM4 12l8 4 8-4M4 17l8 4 8-4', false],
        ['admin.mutasi-stok.index', 'Mutasi stok', 'M7 4v16M3 16l4 4 4-4M17 20V4M13 8l4-4 4 4', false],
        ['admin.stok-produk-jadi.index', 'Stok produk jadi', 'M3 7h18v13H3zM3 7l3-4h12l3 4M9 11h6', false],
        ['admin.daftar-belanja', 'Daftar belanja', 'M3 4h2l2 12h11l2-8H6M9 20h.01M17 20h.01', false],
        ['admin.pengeluaran.index', 'Pengeluaran', 'M3 6h18v12H3zM3 10h18M7 15h3', true],
        ['admin.laporan.index', 'Laporan', 'M4 20V10M10 20V4M16 20v-7M22 20H2', true],
        ['admin.pengguna.index', 'Pengguna', 'M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-4-5.7', true],
        ['admin.faq.index', 'FAQ', 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18zM9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .9-1 1.6V14M12 17h.01', true],
        ['admin.pengaturan.edit', 'Pengaturan', 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM4 6h4M16 6h4M4 18h4M16 18h4M12 3v3M12 18v3', true],
    ];
    $isOwner = auth()->user()->role === 'owner';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.aset')
    <meta name="robots" content="noindex">
    <title>@yield('judul', 'Panel') — Deepsea Florist</title>
    {{-- PWA hanya untuk panel admin, jangan dipasang di layout publik (docs/10-PWA-ADMIN.md) --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#B23A57">
    <link rel="apple-touch-icon" href="/img/pwa/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Deepsea Admin">
</head>
<body>
<div class="admin">
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            <img src="{{ asset('img/logo.png') }}" alt="">
            <span>Deepsea Florist</span>
        </a>
        <ul class="admin-menu">
            @foreach ($menu as [$rute, $label, $ikon, $khususOwner])
                @continue(! Route::has($rute) || ($khususOwner && ! $isOwner))
                <li>
                    @php $pola = Str::endsWith($rute, ['.index', '.edit']) ? Str::beforeLast($rute, '.').'.*' : $rute; @endphp
                    <a href="{{ route($rute) }}" class="{{ request()->routeIs($pola) ? 'aktif' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $ikon }}"/></svg>
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <span class="text-ink-2 small">{{ auth()->user()->name }} · {{ $isOwner ? 'Pemilik' : 'Karyawan' }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-garis btn-sm">Keluar</button>
            </form>
        </div>

        <main class="admin-konten">
            @if (session('sukses'))
                <div class="alert alert-success">{{ session('sukses') }}</div>
            @endif
            @if (session('gagal'))
                <div class="alert alert-danger">{{ session('gagal') }}</div>
            @endif
            @yield('konten')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/admin' }).catch(() => {}); // gagal daftar tidak boleh merusak halaman
    });
}
</script>
</body>
</html>

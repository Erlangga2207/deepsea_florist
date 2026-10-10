@php
    $isOwner = auth()->user()->role === 'owner';
    // Bottom nav HP: [nama route, label, ikon, kunci badge, aktif?]
    $navHp = [
        ['admin.dashboard', 'Beranda', 'M3 11l9-8 9 8M5 9.5V21h14V9.5M10 21v-6h4v6', null, request()->routeIs('admin.dashboard')],
        ['admin.pesanan.index', 'Pesanan', 'M9 3h6v4H9zM9 5H5v16h14V5h-4M9 12h6M9 16h4', 'pesanan_masuk', request()->routeIs('admin.pesanan.*') && ! request()->routeIs('admin.pesanan.create')],
        ['admin.pesanan.create', 'Tambah', 'M12 5v14M5 12h14', null, request()->routeIs('admin.pesanan.create')],
        ['admin.bahan.index', 'Stok', 'M4 7l8-4 8 4-8 4zM4 12l8 4 8-4M4 17l8 4 8-4', 'bahan_menipis', request()->routeIs('admin.bahan.*')],
    ];
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
        <nav class="admin-nav" aria-label="Menu utama">
            @include('layouts.admin-menu')
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand d-lg-none">
                <img src="{{ asset('img/logo.png') }}" alt="">
                <span>Deepsea Florist</span>
            </a>
            <div class="d-none d-lg-flex align-items-center gap-3">
                <span class="text-ink-2 small">{{ auth()->user()->name }} · {{ $isOwner ? 'Pemilik' : 'Karyawan' }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-garis btn-sm">Keluar</button>
                </form>
            </div>
        </header>

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
<nav class="nav-hp d-lg-none" aria-label="Navigasi cepat">
    @foreach ($navHp as [$rute, $label, $ikon, $kunciBadge, $aktif])
        <a href="{{ route($rute) }}" @class(['nav-hp-item', 'aktif' => $aktif, 'nav-hp-tambah' => $rute === 'admin.pesanan.create']) @if ($rute === 'admin.pesanan.create') aria-label="Tambah pesanan" @endif @if ($aktif) aria-current="page" @endif>
            <span class="nav-hp-ikon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $ikon }}"/></svg>
                @if ($kunciBadge && $badgeAdmin[$kunciBadge] > 0)
                    <span class="admin-badge">{{ $badgeAdmin[$kunciBadge] }}</span>
                @endif
            </span>
            {{ $label }}
        </a>
    @endforeach
    <button type="button" class="nav-hp-item" data-bs-toggle="offcanvas" data-bs-target="#menuHp" aria-controls="menuHp">
        <span class="nav-hp-ikon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </span>
        Menu
    </button>
</nav>

<div class="offcanvas offcanvas-bottom admin-offcanvas d-lg-none" tabindex="-1" id="menuHp" aria-labelledby="menuHpJudul">
    <div class="offcanvas-header">
        <h2 class="offcanvas-title fs-6 fw-bold" id="menuHpJudul">Menu</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="admin-nav" aria-label="Menu lengkap">
            @include('layouts.admin-menu')
        </nav>
        <div class="admin-offcanvas-user">
            <span class="text-ink-2">{{ auth()->user()->name }} · {{ $isOwner ? 'Pemilik' : 'Karyawan' }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-garis">Keluar</button>
            </form>
        </div>
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

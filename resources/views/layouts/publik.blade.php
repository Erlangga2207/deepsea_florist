<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.aset')
    @hasSection('seo')
        @yield('seo')
    @else
        <x-seo />
    @endif

    {{-- Data terstruktur: memberi tahu Google ini toko fisik di Subang. Alamat dari cabang utama, cabang lain jadi department.
         Tulis '@@context' (dua @): satu @ dibaca Blade sebagai direktif @@context dan JSON-nya rusak. --}}
    @php
        $lokasiLd = fn ($c) => $c ? [
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $c->alamat,
                'addressLocality' => 'Subang',
                'addressRegion' => 'Jawa Barat',
                'addressCountry' => 'ID',
            ],
            'geo' => ['@type' => 'GeoCoordinates', 'latitude' => (float) $c->latitude, 'longitude' => (float) $c->longitude],
        ] : [];
        $cabangLain = $cabang->reject(fn ($c) => $c->is_utama)->values();
    @endphp
    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@@context' => 'https://schema.org',
            '@type' => 'Florist',
            'name' => $pengaturan->nama_toko,
            'image' => asset('img/logo.png'),
            'url' => url('/'),
            'telephone' => '+'.$pengaturan->no_wa,
            ...$lokasiLd($cabangUtama),
            'hasMap' => $cabangUtama?->url_maps,
            'areaServed' => ['Subang', 'Cibogo', 'Pagaden', 'Pamanukan'],
            'sameAs' => array_values(array_filter([$pengaturan->link_ig, $pengaturan->link_tiktok])) ?: null,
            'department' => $cabangLain->map(fn ($c) => ['@type' => 'Florist', 'name' => $c->nama, ...$lokasiLd($c)])->all() ?: null,
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @stack('kepala')
</head>
<body class="publik">
    <header class="publik-header">
        <nav class="container d-flex align-items-center justify-content-between gap-2 py-2">
            <a href="{{ route('publik.beranda') }}" class="d-flex align-items-center gap-2 text-decoration-none text-body">
                <img src="{{ asset('img/logo.png') }}" alt="{{ $pengaturan->nama_toko }}" width="44" height="44" style="object-fit:contain">
                <span class="fw-bold d-none d-sm-inline">Deepsea Florist</span>
            </a>
            <ul class="nav flex-nowrap">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('publik.beranda') ? 'aktif' : '' }}" href="{{ route('publik.beranda') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('publik.katalog', 'publik.kategori', 'publik.detail') ? 'aktif' : '' }}" href="{{ route('publik.katalog') }}">Katalog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('publik.tentang') ? 'aktif' : '' }}" href="{{ route('publik.tentang') }}">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('publik.kontak') ? 'aktif' : '' }}" href="{{ route('publik.kontak') }}">Kontak</a>
                </li>
            </ul>
            <a href="{{ $pengaturan->linkWa() }}" class="btn btn-primary btn-sm d-none d-sm-inline-flex align-items-center gap-1" target="_blank" rel="noopener">
                <x-ikon-wa /> WhatsApp
            </a>
        </nav>
    </header>

    <main>
        @yield('konten')
    </main>

    <footer class="publik-footer py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-lg-4">
                    <div class="fw-bold text-body mb-2">{{ $pengaturan->nama_toko }}</div>
                    <ul class="list-unstyled small mb-0">
                        @foreach ($cabang as $c)
                            <li class="mb-2"><span class="fw-semibold">{{ $c->nama }}</span><br>{{ $c->alamat }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('publik.kontak') }}" class="small">Lihat peta lokasi</a>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="fw-bold text-body mb-2">Jam buka</div>
                    <p class="small mb-0">{{ $pengaturan->jam_buka ?: '—' }}</p>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="fw-bold text-body mb-2">Hubungi kami</div>
                    <ul class="list-unstyled small mb-0">
                        <li><a href="{{ $pengaturan->linkWa() }}" target="_blank" rel="noopener">WhatsApp {{ preg_replace('/^62/', '0', $pengaturan->no_wa) }}</a></li>
                        @if ($pengaturan->link_ig) <li><a href="{{ $pengaturan->link_ig }}" target="_blank" rel="noopener">Instagram</a></li> @endif
                        @if ($pengaturan->link_tiktok) <li><a href="{{ $pengaturan->link_tiktok }}" target="_blank" rel="noopener">TikTok</a></li> @endif
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="fw-bold text-body mb-2">Jelajahi</div>
                    <ul class="list-unstyled small mb-0">
                        <li><a href="{{ route('publik.katalog') }}">Katalog</a></li>
                        <li><a href="{{ route('publik.beranda') }}#cara-pesan">Cara memesan</a></li>
                        <li><a href="{{ route('publik.tentang') }}">Tentang kami</a></li>
                        <li><a href="{{ route('publik.kontak') }}">Kontak</a></li>
                        <li><a href="{{ route('publik.faq') }}">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="small mt-4 pt-3 border-top">&copy; {{ date('Y') }} {{ $pengaturan->nama_toko }} · Cibogo, Subang</div>
        </div>
    </footer>
</body>
</html>

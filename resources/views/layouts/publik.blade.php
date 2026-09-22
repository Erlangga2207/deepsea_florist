<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.aset')
    @hasSection('seo')
        @yield('seo')
    @else
        <x-seo />
    @endif

    {{-- Data terstruktur: memberi tahu Google ini toko fisik di Subang --}}
    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Florist',
            'name' => $pengaturan->nama_toko,
            'image' => asset('img/logo.png'),
            'url' => url('/'),
            'telephone' => '+'.$pengaturan->no_wa,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $pengaturan->alamat,
                'addressLocality' => 'Subang',
                'addressRegion' => 'Jawa Barat',
                'addressCountry' => 'ID',
            ],
            'geo' => $pengaturan->latitude && $pengaturan->longitude ? [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $pengaturan->latitude,
                'longitude' => (float) $pengaturan->longitude,
            ] : null,
            'hasMap' => $pengaturan->google_maps_url,
            'areaServed' => ['Subang', 'Cibogo', 'Pagaden', 'Pamanukan'],
            'sameAs' => array_values(array_filter([$pengaturan->link_ig, $pengaturan->link_tiktok])) ?: null,
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
                <li class="nav-item d-none d-md-block">
                    <a class="nav-link" href="{{ route('publik.beranda') }}#tentang">Tentang</a>
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
                    <p class="small mb-0">{{ $pengaturan->alamat }}</p>
                    @if ($pengaturan->google_maps_url)
                        <a href="{{ $pengaturan->google_maps_url }}" class="small" target="_blank" rel="noopener">Lihat di Google Maps</a>
                    @endif
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
                        <li><a href="{{ route('publik.beranda') }}#tentang">Tentang kami</a></li>
                    </ul>
                </div>
            </div>
            <div class="small mt-4 pt-3 border-top">&copy; {{ date('Y') }} {{ $pengaturan->nama_toko }} · Cibogo, Subang</div>
        </div>
    </footer>
</body>
</html>

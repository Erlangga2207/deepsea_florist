@props(['produk', 'badge' => null])

<article class="kartu-produk">
    <a href="{{ route('publik.detail', $produk) }}" class="kartu-produk-foto">
        <x-foto-produk :path="$produk->foto_utama" :alt="$produk->nama.', '.Str::lower($produk->kategori->nama).' dari Deepsea Florist Subang'" />
        @if ($badge)
            <x-pill :warna="$badge[1]" :label="$badge[0]" class="kartu-produk-badge" />
        @endif
    </a>
    <div class="kartu-produk-isi">
        <div class="small text-ink-2">{{ $produk->kategori->nama }} · {{ $produk->kode }}</div>
        <h3 class="h6 mb-1"><a href="{{ route('publik.detail', $produk) }}" class="stretched-link text-body text-decoration-none">{{ $produk->nama }}</a></h3>
        <div class="small {{ $produk->tampilkan_harga && $produk->harga_dasar ? 'fw-semibold' : 'text-ink-2' }}">
            @if ($produk->tampilkan_harga && $produk->harga_dasar)
                @rupiah($produk->harga_dasar)
            @else
                Chat untuk harga
            @endif
        </div>
    </div>
</article>

@extends('layouts.publik')

@php
    $pesanWa = \App\Models\Pengaturan::pesanProduk($produk);
    $alt = $produk->nama.', '.Str::lower($produk->kategori->nama).' dari Deepsea Florist Subang';
@endphp

@section('seo')
    <x-seo :judul="$produk->nama.' — Buket Bunga Subang | '.$pengaturan->nama_toko"
           :deskripsi="$produk->meta_deskripsi ?: ($produk->deskripsi ?: $produk->nama.' ('.$produk->kode.'), '.Str::lower($produk->kategori->nama).' dari '.$pengaturan->nama_toko.', Cibogo, Subang. Pesan lewat WhatsApp.')"
           :gambar="$produk->foto_utama ? asset('storage/'.$produk->foto_utama) : null"
           tipe="product" />
@endsection

@push('kepala')
    {{-- Tanpa "offers": harga memang tidak dipublikasikan --}}
    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $produk->nama,
            'sku' => $produk->kode,
            'image' => $produk->foto_utama ? asset('storage/'.$produk->foto_utama) : null,
            'description' => $produk->deskripsi,
            'category' => $produk->kategori->nama,
            'brand' => ['@type' => 'Brand', 'name' => $pengaturan->nama_toko],
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('konten')
    <section class="container py-5">
        <nav class="small mb-3" aria-label="Lokasi halaman">
            <a href="{{ route('publik.katalog') }}">Katalog</a> /
            <a href="{{ route('publik.kategori', $produk->kategori) }}">{{ $produk->kategori->nama }}</a>
        </nav>

        <div class="row g-4 g-lg-5">
            <div class="col-lg-6">
                <x-foto-produk :path="$produk->foto_utama" :alt="$alt" :lazy="false" class="foto-detail" />
                @if ($produk->foto->isNotEmpty())
                    <div class="d-flex gap-2 mt-2 flex-wrap">
                        @foreach ($produk->foto as $f)
                            <a href="{{ Storage::url($f->file) }}" target="_blank" rel="noopener">
                                <x-foto-produk :path="$f->file" :alt="$alt.' — foto '.($loop->index + 2)" class="thumb" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="col-lg-6">
                <div class="text-ink-2 fw-semibold">{{ $produk->kategori->nama }}</div>
                <h1 class="mb-2">{{ $produk->nama }}</h1>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <x-pill :warna="$badge[1]" :label="$badge[0]" />
                    <span class="text-ink-2">Kode model <strong class="text-body">{{ $produk->kode }}</strong></span>
                </div>

                @if ($produk->deskripsi)
                    <p class="mb-4">{{ $produk->deskripsi }}</p>
                @endif

                <dl class="spesifikasi">
                    <dt>Kategori</dt><dd>{{ $produk->kategori->nama }}</dd>
                    <dt>Ukuran standar</dt><dd>Normal</dd>
                    <dt>Perkiraan pengerjaan</dt><dd>{{ $produk->status === 'preorder' ? 'Pre-order, jadwal dibicarakan lewat chat' : '1–3 hari' }}</dd>
                    <dt>Ukuran khusus</dt><dd>Bisa, dibicarakan lewat chat</dd>
                    <dt>Harga</dt>
                    <dd>
                        @if ($produk->tampilkan_harga && $produk->harga_dasar) @rupiah($produk->harga_dasar) @else Dibicarakan lewat chat @endif
                    </dd>
                </dl>

                <a href="{{ $pengaturan->linkWa($pesanWa) }}" class="btn btn-primary btn-lg w-100 d-inline-flex justify-content-center align-items-center gap-2 mb-3" target="_blank" rel="noopener" id="tombol-wa">
                    <x-ikon-wa width="22" height="22" /> Pesan lewat WhatsApp
                </a>
                <div class="pratinjau-pesan">
                    <div class="small text-ink-2 mb-1">Pesan yang akan terkirim:</div>
                    <div>{{ $pesanWa }}</div>
                </div>
            </div>
        </div>

        @if ($serupa->isNotEmpty())
            <h2 class="h4 mt-5 mb-3">Model serupa</h2>
            <div class="row row-cols-2 row-cols-lg-3 g-3">
                @foreach ($serupa as $p)
                    <div class="col"><x-kartu-produk :produk="$p" /></div>
                @endforeach
            </div>
        @endif
    </section>
@endsection

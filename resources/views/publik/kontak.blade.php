@extends('layouts.publik')

@section('seo')
    <x-seo :judul="'Kontak '.$pengaturan->nama_toko.' — Toko Bunga di Subang'"
           :deskripsi="'Alamat, jam buka, dan WhatsApp '.$pengaturan->nama_toko.'. '.$cabangUtama?->alamat" />
@endsection

@section('konten')
    <section class="container py-5">
        <p class="text-ink-2 fw-semibold mb-2">Cibogo, Subang</p>
        <h1 class="mb-4">Kontak &amp; lokasi</h1>

        <div class="row g-4 g-lg-5 mb-5">
            <div class="col-lg-7">
                <dl class="info-kontak">
                    <div>
                        <dt>WhatsApp</dt>
                        <dd><a href="{{ $pengaturan->linkWa() }}" target="_blank" rel="noopener">{{ preg_replace('/^62/', '0', $pengaturan->no_wa) }}</a></dd>
                    </div>
                    @if ($pengaturan->link_ig || $pengaturan->link_tiktok)
                        <div>
                            <dt>Media sosial</dt>
                            <dd class="d-flex gap-3">
                                @if ($pengaturan->link_ig) <a href="{{ $pengaturan->link_ig }}" target="_blank" rel="noopener">Instagram</a> @endif
                                @if ($pengaturan->link_tiktok) <a href="{{ $pengaturan->link_tiktok }}" target="_blank" rel="noopener">TikTok</a> @endif
                            </dd>
                        </div>
                    @endif
                </dl>
                <a href="{{ $pengaturan->linkWa() }}" class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2" target="_blank" rel="noopener"><x-ikon-wa /> Chat WhatsApp</a>
            </div>
            <div class="col-lg-5">
                <p class="small text-ink-2 mb-2">Mau ambil pesanan langsung ke toko? Kabari dulu lewat WhatsApp supaya buketnya sudah siap saat Anda datang.</p>
                <p class="small text-ink-2 mb-0">Soal pengantaran, pembayaran, dan lama pengerjaan: <a href="{{ route('publik.faq') }}">lihat FAQ</a>.</p>
            </div>
        </div>

        @foreach ($cabang as $c)
            <article class="row g-4 g-lg-5 py-4 border-top">
                <div class="col-lg-5">
                    <h2 class="h4 mb-2">{{ $c->nama }}</h2>
                    @if ($c->is_utama) <x-pill warna="rose" label="Cabang utama" class="mb-3" /> @endif
                    <dl class="info-kontak">
                        <div>
                            <dt>Alamat</dt>
                            <dd><address class="mb-0">{{ $c->alamat }}</address></dd>
                        </div>
                        @if ($c->jam_buka)
                            <div>
                                <dt>Jam buka</dt>
                                <dd>{{ $c->jam_buka }}</dd>
                            </div>
                        @endif
                    </dl>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ $c->arah_url }}" class="btn btn-garis" target="_blank" rel="noopener">Petunjuk arah</a>
                        @if ($c->no_wa)
                            <a href="https://wa.me/{{ $c->no_wa }}" class="btn btn-garis d-inline-flex align-items-center gap-2" target="_blank" rel="noopener"><x-ikon-wa /> WhatsApp</a>
                        @endif
                        @if ($c->url_maps)
                            <a href="{{ $c->url_maps }}" class="btn btn-polos" target="_blank" rel="noopener">Lihat di Google Maps</a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-7">
                    <iframe class="peta" title="Peta lokasi {{ $c->nama }}" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" src="{{ $c->embed_url }}"></iframe>
                </div>
            </article>
        @endforeach
    </section>
@endsection

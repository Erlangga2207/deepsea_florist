@extends('layouts.publik')

@section('seo')
    <x-seo :judul="'Kontak '.$pengaturan->nama_toko.' — Toko Bunga di Subang'"
           :deskripsi="'Alamat, jam buka, dan WhatsApp '.$pengaturan->nama_toko.'. '.$pengaturan->alamat" />
@endsection

@php $lokasi = rawurlencode($pengaturan->lokasiPeta()); @endphp

@section('konten')
    <section class="container py-5">
        <p class="text-ink-2 fw-semibold mb-2">Cibogo, Subang</p>
        <h1 class="mb-4">Kontak &amp; lokasi</h1>

        <div class="row g-4 g-lg-5">
            <div class="col-lg-5 order-2 order-lg-1">
                <dl class="info-kontak">
                    <div>
                        <dt>Alamat</dt>
                        <dd><address class="mb-0">{{ $pengaturan->alamat }}</address></dd>
                    </div>
                    <div>
                        <dt>Jam buka</dt>
                        <dd>{{ $pengaturan->jam_buka ?: '—' }}</dd>
                    </div>
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
                <p class="small text-ink-2 mt-3 mb-0">Mau ambil pesanan langsung ke toko? Kabari dulu lewat WhatsApp supaya buketnya sudah siap saat Anda datang.</p>
                <p class="small text-ink-2 mt-2 mb-0">Soal pengantaran, pembayaran, dan lama pengerjaan: <a href="{{ route('publik.faq') }}">lihat FAQ</a>.</p>
            </div>

            <div class="col-lg-7 order-1 order-lg-2">
                <iframe class="peta" title="Peta lokasi {{ $pengaturan->nama_toko }}" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://maps.google.com/maps?q={{ $lokasi }}&z=16&output=embed"></iframe>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $lokasi }}" class="btn btn-garis" target="_blank" rel="noopener">Petunjuk arah</a>
                    @if ($pengaturan->google_maps_url)
                        <a href="{{ $pengaturan->google_maps_url }}" class="btn btn-polos" target="_blank" rel="noopener">Buka di Google Maps</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('layouts.publik')

@section('seo')
    <x-seo :judul="'Tentang '.$pengaturan->nama_toko.' — Toko Bunga Cibogo, Subang'"
           :deskripsi="'Kenalan dengan '.$pengaturan->nama_toko.', toko bunga rumahan di Cibogo, Subang yang merangkai buket dan bunga papan sejak 2023.'" />
@endsection

@section('konten')
    {{-- Latar foto hanya di halaman ini (docs/09 bagian 2) --}}
    <section class="tentang-hero">
        <div class="tentang-hero-isi container text-center">
            <p class="fw-semibold text-ink-2 mb-2">Sejak 2023 · Cibogo, Subang</p>
            <h1 class="display-5 mb-3">Tentang {{ $pengaturan->nama_toko }}</h1>
            <div class="cerita mx-auto mb-4">
                @if ($pengaturan->tentang)
                    {!! nl2br(e($pengaturan->tentang)) !!}
                @else
                    {{ $pengaturan->nama_toko }} adalah toko bunga rumahan di Cinangsi, Cibogo. Setiap buket dan bunga papan kami rangkai sendiri dengan tangan, mengikuti warna dan momen yang diminta pelanggan.
                @endif
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route('publik.katalog') }}" class="btn btn-primary btn-lg">Lihat katalog</a>
                <a href="{{ $pengaturan->linkWa() }}" class="btn btn-garis btn-lg d-inline-flex align-items-center gap-2" target="_blank" rel="noopener"><x-ikon-wa /> Chat WhatsApp</a>
            </div>
        </div>
    </section>

    <section class="container">
        <dl class="fakta">
            <div><dt>Mulai merangkai</dt><dd class="font-display">2023</dd></div>
            <div><dt>Pilihan produk</dt><dd class="font-display">{{ $kategori->count() }} jenis</dd></div>
            <div><dt>Waktu pengerjaan</dt><dd class="font-display">1–3 hari</dd></div>
        </dl>
    </section>

    <section class="container py-5">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-4">
                <h2 class="h3">Cara kami bekerja</h2>
                <p class="text-ink-2 mb-0">Toko kecil, jadi setiap pesanan kami pegang sendiri dari chat pertama sampai buketnya diserahkan.</p>
            </div>
            <div class="col-lg-8">
                <div class="daftar-garis">
                    <div>
                        <h3 class="h5">Dirangkai setelah dipesan</h3>
                        <p class="text-ink-2 mb-0">Buket mulai dikerjakan setelah DP 50% masuk, bukan diambil dari rak. Bunganya masih segar saat sampai di tangan Anda.</p>
                    </div>
                    <div>
                        <h3 class="h5">Bunga segar dibeli tiap minggu</h3>
                        <p class="text-ink-2 mb-0">Bunga fresh kami belanja sendiri seminggu sekali, jadi stok tidak menumpuk sampai layu.</p>
                    </div>
                    <div>
                        <h3 class="h5">Setiap model punya kode</h3>
                        <p class="text-ink-2 mb-0">Sebutkan kode seperti DF-014 saat chat, kami langsung tahu model yang Anda maksud. Punya contoh foto sendiri? Kirim saja, kami bantu buatkan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($kategori->isNotEmpty())
        <section class="container pb-5">
            <h2 class="h3 mb-3">Yang kami rangkai</h2>
            <ul class="daftar-kategori">
                @foreach ($kategori as $k)
                    <li>
                        <a href="{{ route('publik.kategori', $k) }}">
                            <span class="fw-semibold">{{ $k->nama }}</span>
                            @if ($k->produk_count) <span class="text-ink-2 small">{{ $k->produk_count }} model</span> @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="container pb-4">
        <div class="kotak p-4 p-lg-5 d-lg-flex align-items-center justify-content-between gap-4">
            <div class="mb-3 mb-lg-0">
                <h2 class="h3 mb-2">Masih ada pertanyaan?</h2>
                <p class="text-ink-2 mb-0">Lama pengerjaan, pengantaran, sampai cara merawat buket sudah kami jawab di halaman FAQ.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                <a href="{{ route('publik.faq') }}" class="btn btn-garis btn-lg">Baca FAQ</a>
                <a href="{{ $pengaturan->linkWa() }}" class="btn btn-polos btn-lg d-inline-flex align-items-center gap-2" target="_blank" rel="noopener"><x-ikon-wa /> Tanya lewat WhatsApp</a>
            </div>
        </div>
    </section>
@endsection

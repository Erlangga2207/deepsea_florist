@extends('layouts.publik')

@section('seo')
    <x-seo :judul="$pengaturan->nama_toko.' — Toko Bunga & Buket di Subang'" />
@endsection

@section('konten')
    <section class="container py-5">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6">
                <p class="text-ink-2 fw-semibold mb-2">Toko bunga di Cibogo, Subang</p>
                <h1 class="display-5 mb-3">Buket dan bunga papan, dirangkai sendiri untuk momen Anda</h1>
                <p class="lead text-ink-2 mb-4">Buket wisuda, buket fresh dan artificial, buket uang, buket snack, sampai bunga papan ucapan. Pilih modelnya di katalog, lalu pesan lewat WhatsApp.</p>
                <div class="d-flex flex-wrap gap-2 mb-5">
                    <a href="{{ route('publik.katalog') }}" class="btn btn-primary btn-lg">Lihat katalog</a>
                    <a href="{{ $pengaturan->linkWa() }}" class="btn btn-garis btn-lg d-inline-flex align-items-center gap-2" target="_blank" rel="noopener"><x-ikon-wa /> Chat WhatsApp</a>
                </div>
                <dl class="row g-3 mb-0 angka-hero">
                    <div class="col-4"><dt class="font-display">2023</dt><dd>Mulai merangkai</dd></div>
                    <div class="col-4"><dt class="font-display">{{ $kategori->count() }} jenis</dt><dd>Pilihan produk</dd></div>
                    <div class="col-4"><dt class="font-display">1–3 hari</dt><dd>Waktu pengerjaan</dd></div>
                </dl>
            </div>
            <div class="col-lg-6">
                <x-foto-produk :path="$fotoHero" :lazy="false" alt="Buket bunga rangkaian Deepsea Florist Subang" class="foto-hero" />
            </div>
        </div>
    </section>

    <section class="container pb-4">
        <div class="chip-kategori">
            @foreach ($kategori as $k)
                <a href="{{ route('publik.kategori', $k) }}" class="chip">{{ $k->nama }}</a>
            @endforeach
        </div>
    </section>

    <section class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h2 class="h3 mb-0">Paling sering dipesan</h2>
            <a href="{{ route('publik.katalog') }}">Semua model</a>
        </div>
        <div class="row row-cols-2 row-cols-lg-4 g-3">
            @foreach ($terlaris as $p)
                <div class="col"><x-kartu-produk :produk="$p" :badge="$badge[$p->id]" /></div>
            @endforeach
        </div>
    </section>

    <section class="container py-5" id="cara-pesan">
        <h2 class="h3 mb-4">Cara memesan</h2>
        <ol class="langkah">
            <li>
                <h3 class="h5">Pilih model</h3>
                <p class="text-ink-2 mb-0">Lihat katalog dan catat kode modelnya, misalnya DF-014. Model di luar katalog juga bisa dibuat.</p>
            </li>
            <li>
                <h3 class="h5">Chat WhatsApp</h3>
                <p class="text-ink-2 mb-0">Sebutkan kode model, tanggal dibutuhkan, dan warna. Harga dibicarakan lewat chat.</p>
            </li>
            <li>
                <h3 class="h5">Bayar DP 50%</h3>
                <p class="text-ink-2 mb-0">Pesanan mulai dikerjakan setelah DP masuk. Pelunasan saat buket diambil atau diantar.</p>
            </li>
        </ol>
    </section>

    <section class="container py-4" id="tentang">
        <div class="kotak p-4 p-lg-5">
            <h2 class="h3">Tentang {{ $pengaturan->nama_toko }}</h2>
            <p class="text-ink-2 mb-0" style="max-width:65ch">
                {{ $pengaturan->tentang ?: 'Toko bunga rumahan di Cinangsi, Cibogo, Subang. Merangkai buket dan bunga papan untuk wisuda, ulang tahun, pernikahan, dan pembukaan usaha di Subang dan sekitarnya.' }}
            </p>
        </div>
    </section>
@endsection

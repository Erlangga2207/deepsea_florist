@extends('layouts.publik')

@section('seo')
    @if ($kategoriAktif)
        <x-seo :judul="$kategoriAktif->nama.' di Subang — '.$pengaturan->nama_toko"
               :deskripsi="$kategoriAktif->meta_deskripsi ?: $kategoriAktif->deskripsi" />
    @else
        <x-seo :judul="'Katalog Buket Bunga di Subang — '.$pengaturan->nama_toko" />
    @endif
@endsection

@section('konten')
    <section class="container py-5">
        <h1 class="mb-2">{{ $kategoriAktif ? $kategoriAktif->nama.' di Subang' : 'Katalog' }}</h1>
        @if ($kategoriAktif?->deskripsi)
            <p class="lead text-ink-2" style="max-width:70ch">{{ $kategoriAktif->deskripsi }}</p>
        @endif

        <div class="chip-kategori my-4">
            <a href="{{ route('publik.katalog') }}" class="chip {{ $kategoriAktif ? '' : 'aktif' }}">Semua</a>
            @foreach ($semuaKategori as $k)
                <a href="{{ route('publik.kategori', $k) }}" class="chip {{ $kategoriAktif?->is($k) ? 'aktif' : '' }}">{{ $k->nama }}</a>
            @endforeach
        </div>

        <p class="text-ink-2">Menampilkan {{ $produk->count() }} dari {{ $totalModel }} model</p>

        @if ($produk->isEmpty())
            <div class="kotak p-4 text-ink-2">
                Belum ada model di kategori ini. <a href="{{ $pengaturan->linkWa() }}" target="_blank" rel="noopener">Tanyakan lewat WhatsApp</a> — model custom bisa dibuat.
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($produk as $p)
                    <div class="col"><x-kartu-produk :produk="$p" :badge="$badge[$p->id]" /></div>
                @endforeach
            </div>
        @endif
    </section>
@endsection

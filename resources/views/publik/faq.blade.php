@extends('layouts.publik')

@section('seo')
    <x-seo :judul="'Pertanyaan Seputar Buket dan Bunga — '.$pengaturan->nama_toko"
           :deskripsi="'Jawaban untuk pertanyaan yang paling sering masuk ke '.$pengaturan->nama_toko.': daya tahan bunga fresh, lama pengerjaan, pengantaran, dan cara pembayaran.'" />
@endsection

{{-- Hanya di halaman ini, isinya sama persis dengan yang tampil (docs/09 bagian 1.5) --}}
@push('kepala')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faq->map(fn ($f) => [
                '@type' => 'Question',
                'name' => $f->pertanyaan,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->jawaban],
            ])->values(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('konten')
    <section class="container py-5" style="max-width:820px">
        <h1 class="mb-2">Pertanyaan yang sering ditanyakan</h1>
        <p class="lead text-ink-2 mb-4">Belum menemukan jawabannya? Tanyakan langsung lewat WhatsApp.</p>

        <div class="faq">
            @foreach ($faq as $item)
                <details class="faq-item">
                    <summary>
                        <h2 class="h6 mb-0">{{ $item->pertanyaan }}</h2>
                        <x-ikon-panah class="faq-panah" />
                    </summary>
                    <div class="faq-jawaban">{!! nl2br(e($item->jawaban)) !!}</div>
                </details>
            @endforeach
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="{{ $pengaturan->linkWa() }}" class="btn btn-primary d-inline-flex align-items-center gap-2" target="_blank" rel="noopener"><x-ikon-wa /> Chat WhatsApp</a>
            <a href="{{ route('publik.katalog') }}" class="btn btn-garis">Lihat katalog</a>
        </div>
    </section>
@endsection

{{-- Foto produk rasio 4:5, WebP bila tersedia. Tanpa foto → bidang polos berlogo, bukan gambar rusak. --}}
@props(['path' => null, 'alt' => '', 'lazy' => true])

@if ($path)
    @php $webp = \App\Support\FotoWebp::pathUntuk($path); @endphp
    <picture>
        @if ($webp !== $path && Storage::disk('public')->exists($webp))
            <source srcset="{{ Storage::url($webp) }}" type="image/webp">
        @endif
        <img src="{{ Storage::url($path) }}" alt="{{ $alt }}" width="800" height="1000"
             @if ($lazy) loading="lazy" @endif {{ $attributes->class('foto-produk') }}>
    </picture>
@else
    <div {{ $attributes->class('foto-produk foto-kosong') }} role="img" aria-label="{{ $alt }} (foto belum tersedia)">
        <img src="{{ asset('img/logo.png') }}" alt="" width="96" height="96" loading="lazy">
    </div>
@endif

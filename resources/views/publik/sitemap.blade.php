{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('publik.beranda') }}</loc></url>
    <url><loc>{{ route('publik.katalog') }}</loc>@if ($produk->isNotEmpty())<lastmod>{{ $produk->max('updated_at')->toAtomString() }}</lastmod>@endif</url>
@foreach ($kategori as $k)
    <url><loc>{{ route('publik.kategori', $k) }}</loc><lastmod>{{ $k->updated_at->toAtomString() }}</lastmod></url>
@endforeach
@foreach ($produk as $p)
    <url><loc>{{ route('publik.detail', $p) }}</loc><lastmod>{{ $p->updated_at->toAtomString() }}</lastmod></url>
@endforeach
</urlset>

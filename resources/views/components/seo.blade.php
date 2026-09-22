@props(['judul' => null, 'deskripsi' => null, 'gambar' => null, 'tipe' => 'website'])

@php
    $judul ??= $pengaturan->meta_judul_default ?: $pengaturan->nama_toko;
    $deskripsi = Str::limit(strip_tags($deskripsi ?? $pengaturan->meta_deskripsi_default ?? ''), 155, '…');
    $gambar ??= asset('img/logo.png');
@endphp

<title>{{ $judul }}</title>
<meta name="description" content="{{ $deskripsi }}">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="{{ $tipe }}">
<meta property="og:title" content="{{ $judul }}">
<meta property="og:description" content="{{ $deskripsi }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ $gambar }}">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="{{ $pengaturan->nama_toko }}">
<meta name="twitter:card" content="summary_large_image">

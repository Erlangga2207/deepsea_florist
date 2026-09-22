<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.aset')
    <meta name="robots" content="noindex">
    <title>@yield('judul') — Deepsea Florist</title>
</head>
<body>
<main class="min-vh-100 d-flex align-items-center justify-content-center p-3 text-center">
    <div style="max-width:440px">
        <img src="{{ asset('img/logo.png') }}" alt="Deepsea Florist" width="80" height="80" style="object-fit:contain">
        <div class="font-display text-ink-2 mt-3" style="font-size:3rem;line-height:1">@yield('kode')</div>
        <h1 class="h4 fw-bold mt-2">@yield('judul')</h1>
        <p class="text-ink-2">@yield('pesan')</p>
        <div class="d-flex justify-content-center gap-2">
            @auth
                <a href="{{ url('/admin') }}" class="btn btn-primary">Ke dashboard</a>
            @else
                <a href="{{ url('/') }}" class="btn btn-primary">Ke beranda</a>
            @endauth
            <button type="button" class="btn btn-garis" onclick="history.back()">Kembali</button>
        </div>
    </div>
</main>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.aset')
    <meta name="robots" content="noindex">
    <title>Masuk — Deepsea Florist</title>
</head>
<body>
<main class="min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="kotak p-4 w-100" style="max-width:380px">
        <div class="text-center mb-4">
            <img src="{{ asset('img/logo.png') }}" alt="Deepsea Florist" width="72" height="72" style="object-fit:contain">
            <h1 class="h5 fw-bold mt-2 mb-0">Masuk ke panel</h1>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       required autofocus autocomplete="username">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata sandi</label>
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required autocomplete="current-password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-check mb-3">
                <input id="remember" type="checkbox" name="remember" class="form-check-input">
                <label for="remember" class="form-check-label">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>

        <p class="small text-ink-2 text-center mt-3 mb-0">Lupa kata sandi? Minta pemilik toko mengaturnya ulang.</p>
    </div>
</main>
</body>
</html>

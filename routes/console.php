<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

// Membuat akun pemilik pertama di hosting (tidak ada halaman daftar). Kata sandi diketik sendiri, tidak tampil di layar.
Artisan::command('akun:owner', function () {
    $data = [
        'name' => $this->ask('Nama pemilik'),
        'email' => $this->ask('Email untuk masuk'),
        'password' => $this->secret('Kata sandi (minimal 8 karakter)'),
    ];

    $cek = Validator::make($data, [
        'name' => ['required', 'max:100'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'min:8'],
    ]);
    if ($cek->fails()) {
        foreach ($cek->errors()->all() as $pesan) {
            $this->error($pesan);
        }

        return 1;
    }

    User::create($data + ['role' => 'owner']);
    $this->info("Akun pemilik {$data['email']} dibuat. Akun karyawan dibuat dari menu Pengguna.");
})->purpose('Buat akun pemilik toko');

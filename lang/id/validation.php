<?php

// Hanya aturan yang dipakai di aplikasi ini. Tambah seperlunya.
return [
    'alpha_dash' => ':Attribute hanya boleh huruf kecil, angka, dan tanda hubung.',
    'array' =>':Attribute tidak valid.',
    'between' => [
        'numeric' => ':Attribute harus antara :min dan :max.',
    ],
    'boolean' =>':Attribute harus ya atau tidak.',
    'confirmed' => 'Konfirmasi :attribute tidak sama.',
    'date' => ':Attribute bukan tanggal yang benar.',
    'after_or_equal' => ':Attribute tidak boleh sebelum :date.',
    'distinct' => ':Attribute dipilih lebih dari sekali.',
    'email' => ':Attribute harus berupa alamat email.',
    'exists' => ':Attribute yang dipilih tidak ada.',
    'file' => ':Attribute harus berupa file.',
    'gt' => [
        'numeric' => ':Attribute harus lebih dari :value.',
    ],
    'gte' => [
        'numeric' => ':Attribute minimal :value.',
    ],
    'image' => ':Attribute harus berupa gambar.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus bilangan bulat.',
    'max' => [
        'file' => ':Attribute maksimal :max KB.',
        'numeric' => ':Attribute maksimal :max.',
        'string' => ':Attribute maksimal :max karakter.',
    ],
    'mimes' => ':Attribute harus berformat :values.',
    'min' => [
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
    ],
    'numeric' => ':Attribute harus berupa angka.',
    'required' => ':Attribute wajib diisi.',
    'required_if' => ':Attribute wajib diisi.',
    'required_with' => ':Attribute wajib diisi.',
    'required_without' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah dipakai.',
    'uploaded' => ':Attribute gagal diunggah.',
    'url' => ':Attribute harus berupa tautan yang benar.',

    'custom' => [],

    'attributes' => [
        'name' => 'nama',
        'password' => 'kata sandi',
        'kategori_id' => 'kategori',
        'bahan_id' => 'bahan',
        'komposisi.*.bahan_id' => 'bahan',
        'komposisi.*.jumlah' => 'jumlah',
        'galeri.*' => 'foto galeri',
        'no_wa' => 'nomor WhatsApp',
        'nama_pelanggan' => 'nama pemesan',
        'dp_metode' => 'cara bayar DP',
        'tanggal_jadi' => 'tanggal jadi',
        'qty' => 'jumlah',
        'link_ig' => 'link Instagram',
        'link_tiktok' => 'link TikTok',
    ],
];

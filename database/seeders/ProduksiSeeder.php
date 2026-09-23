<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

// Data yang juga dibutuhkan di hosting: pengaturan toko + 6 kategori. Tanpa data contoh.
// Di hosting: php artisan db:seed --class=ProduksiSeeder --force, lalu php artisan akun:owner
class ProduksiSeeder extends Seeder
{
    public function run(): void
    {
        Pengaturan::firstOrCreate(['id' => 1], [
            'nama_toko' => 'Deepsea Florist Home Store',
            'alamat' => 'Jl. Raya Cinangsi RT.01/RW.01, Cinangsi, Kec. Cibogo, Kabupaten Subang 41211',
            'jam_buka' => 'Senin–Sabtu, 08.00–17.00',
            'no_wa' => '6281296836363',
            'jumlah_perakit' => 3,
            'jam_kerja_per_hari' => 6,
            'margin_default' => 1.60,
            'tarif_jasa_per_jam' => 10000,
            'meta_judul_default' => 'Deepsea Florist — Toko Bunga & Buket di Subang',
            'meta_deskripsi_default' => 'Toko bunga di Cibogo, Subang. Buket wisuda, buket fresh dan artificial, buket uang, buket snack, dan bunga papan. Pesan lewat WhatsApp.',
        ]);

        $kategori = [
            ['Buket artificial', 'buket-artificial', 'artificial'],
            ['Buket fresh', 'buket-fresh', 'fresh'],
            ['Buket uang', 'buket-uang', 'uang'],
            ['Buket snack', 'buket-snack', 'umum'],
            ['Bunga papan', 'bunga-papan', 'papan'],
            ['Dekorasi', 'dekorasi', 'umum'],
        ];

        foreach ($kategori as $i => [$nama, $slug, $tipe]) {
            Kategori::firstOrCreate(['slug' => $slug], ['nama' => $nama, 'tipe_harga' => $tipe, 'urutan' => $i + 1]);
        }

        $this->call(FaqSeeder::class);
    }
}

<?php

namespace App\Http\Controllers\Publik;

use App\Domain\Stok\CekKelayakan;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;

class BerandaController extends Controller
{
    public function __invoke(CekKelayakan $cek)
    {
        // "Paling sering dipesan": urut jumlah item pesanan, 4 teratas
        $terlaris = Produk::with('kategori', 'komposisi.bahan')
            ->where('is_aktif', true)
            ->withCount('pesananItem')
            ->orderByDesc('pesanan_item_count')
            ->orderBy('kode')
            ->take(4)
            ->get();

        return view('publik.beranda', [
            'terlaris' => $terlaris,
            'badge' => $terlaris->mapWithKeys(fn ($p) => [$p->id => $cek->badge($p)]),
            'kategori' => Kategori::orderBy('urutan')->get(),
            'fotoHero' => Produk::where('is_aktif', true)->whereNotNull('foto_utama')->value('foto_utama'),
        ]);
    }
}

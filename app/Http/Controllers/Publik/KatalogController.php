<?php

namespace App\Http\Controllers\Publik;

use App\Domain\Stok\CekKelayakan;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;

class KatalogController extends Controller
{
    public function __construct(private CekKelayakan $cek) {}

    public function index()
    {
        return $this->tampil(null);
    }

    public function kategori(Kategori $kategori)
    {
        return $this->tampil($kategori);
    }

    public function detail(Produk $produk)
    {
        abort_unless($produk->is_aktif, 404);
        $produk->load('kategori', 'foto', 'komposisi.bahan');

        $serupa = Produk::with('kategori')
            ->where('is_aktif', true)
            ->where('kategori_id', $produk->kategori_id)
            ->whereKeyNot($produk->id)
            ->orderBy('kode')
            ->take(3)
            ->get();

        return view('publik.detail', [
            'produk' => $produk,
            'badge' => $this->cek->badge($produk),
            'serupa' => $serupa,
        ]);
    }

    private function tampil(?Kategori $kategori)
    {
        $produk = Produk::with('kategori', 'komposisi.bahan')
            ->where('is_aktif', true)
            ->when($kategori, fn ($q) => $q->where('kategori_id', $kategori->id))
            ->orderBy('kode')
            ->get();

        return view('publik.katalog', [
            'produk' => $produk,
            'badge' => $produk->mapWithKeys(fn ($p) => [$p->id => $this->cek->badge($p)]),
            'semuaKategori' => Kategori::orderBy('urutan')->get(),
            'kategoriAktif' => $kategori,
            'totalModel' => Produk::where('is_aktif', true)->count(),
        ]);
    }
}

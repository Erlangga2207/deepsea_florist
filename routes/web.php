<?php

use App\Http\Controllers\Admin\BahanController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\MutasiStokController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PengeluaranController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\StokProdukJadiController;
use App\Http\Controllers\Publik\BerandaController;
use App\Http\Controllers\Publik\KatalogController;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Support\Facades\Route;

Route::get('/', BerandaController::class)->name('publik.beranda');
Route::get('/katalog', [KatalogController::class, 'index'])->name('publik.katalog');
Route::get('/katalog/kategori/{kategori:slug}', [KatalogController::class, 'kategori'])->name('publik.kategori');
Route::get('/katalog/{produk:slug}', [KatalogController::class, 'detail'])->name('publik.detail');

// Dibuat dari database supaya tidak basi (docs/08-SEO.md A6)
Route::get('/sitemap.xml', fn () => response()
    ->view('publik.sitemap', [
        'produk' => Produk::where('is_aktif', true)->get(),
        'kategori' => Kategori::all(),
    ])
    ->header('Content-Type', 'application/xml'))->name('sitemap');

// Halaman admin wajib diblokir dari mesin pencari
Route::get('/robots.txt', fn () => response(
    "User-agent: *\nDisallow: /admin\nDisallow: /login\nAllow: /\n\nSitemap: ".route('sitemap')."\n"
)->header('Content-Type', 'text/plain'));

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::post('pesanan/cek-kelayakan', [PesananController::class, 'cek'])->name('pesanan.cek');
    Route::resource('pesanan', PesananController::class)->except('destroy');
    Route::post('pesanan/{pesanan}/status', [PesananController::class, 'ubahStatus'])->name('pesanan.status');
    Route::post('pesanan/{pesanan}/pembayaran', [PesananController::class, 'bayar'])->name('pesanan.pembayaran');

    Route::resource('kategori', KategoriController::class)->except('show');

    Route::resource('produk', ProdukController::class);
    Route::put('produk/{produk}/komposisi', [ProdukController::class, 'simpanKomposisi'])->name('produk.komposisi');
    Route::delete('produk/{produk}/foto/{foto}', [ProdukController::class, 'hapusFoto'])->name('produk.foto.hapus')->scopeBindings();

    Route::resource('bahan', BahanController::class)->except('show');
    Route::resource('mutasi-stok', MutasiStokController::class)->only(['index', 'create', 'store']);
    Route::get('daftar-belanja', [BahanController::class, 'daftarBelanja'])->name('daftar-belanja');
    Route::resource('stok-produk-jadi', StokProdukJadiController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::middleware('owner')->group(function () {
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
        Route::resource('pengeluaran', PengeluaranController::class)->except('show');
        Route::resource('pengguna', PenggunaController::class)->except(['show', 'destroy']);
        Route::get('pengaturan', [PengaturanController::class, 'edit'])->name('pengaturan.edit');
        Route::put('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    });
});

require __DIR__.'/auth.php';

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StokProdukJadiRequest;
use App\Models\Produk;
use App\Models\StokProdukJadi;
use Illuminate\Http\Request;

// Buket yang sudah jadi dan siap dijual langsung (dari pesanan batal atau dibuat untuk dipajang).
class StokProdukJadiController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'tersedia');

        return view('admin.stok-produk-jadi.index', [
            'stok' => StokProdukJadi::with('produk', 'pesanan')->where('status', $status)->latest()->get(),
            'status' => $status,
            'produk' => Produk::where('is_aktif', true)->orderBy('nama')->get(),
        ]);
    }

    // Buket yang sengaja dibuat untuk dipajang. Bahannya dicatat terpisah lewat Mutasi Stok bila perlu.
    public function store(StokProdukJadiRequest $request)
    {
        $produk = $request->validated('produk_id') ? Produk::find($request->validated('produk_id')) : null;

        StokProdukJadi::create([
            'produk_id' => $produk?->id,
            'nama' => $request->validated('nama') ?: $produk->nama,
            'foto' => $produk?->foto_utama,
            'harga_jual' => $request->validated('harga_jual'),
            'asal' => 'produksi',
        ]);

        return back()->with('sukses', 'Buket jadi dicatat.');
    }

    public function update(StokProdukJadiRequest $request, StokProdukJadi $stokProdukJadi)
    {
        $stokProdukJadi->update([
            'status' => $stokProdukJadi->status === 'tersedia' ? 'terjual' : 'tersedia',
            'harga_jual' => $request->validated('harga_jual') ?? $stokProdukJadi->harga_jual,
        ]);

        return back()->with('sukses', "{$stokProdukJadi->nama} ditandai {$stokProdukJadi->status}.");
    }

    public function destroy(StokProdukJadi $stokProdukJadi)
    {
        $stokProdukJadi->delete();

        return back()->with('sukses', 'Catatan buket jadi dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Stok\MutasiStokService;
use App\Http\Controllers\Controller;
use App\Http\Requests\BahanRequest;
use App\Models\Bahan;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    public const JENIS = ['fresh' => 'Fresh', 'artificial' => 'Artificial', 'pendukung' => 'Pendukung'];

    public function index(Request $request)
    {
        $bahan = Bahan::with('masukTerakhir')
            ->when($request->jenis, fn ($q, $jenis) => $q->where('jenis', $jenis))
            ->orderBy('jenis')
            ->orderBy('nama')
            ->get();

        return view('admin.bahan.index', ['bahan' => $bahan, 'jenis' => self::JENIS]);
    }

    // Dibuka saat belanja ke Lembang: bahan di bawah minimum + kebutuhan pesanan yang belum dikerjakan
    public function daftarBelanja()
    {
        $kebutuhan = [];
        $pesananMasuk = Pesanan::where('status', 'masuk')->with('item.produk.komposisi')->get();
        foreach ($pesananMasuk as $pesanan) {
            foreach ($pesanan->item as $item) {
                foreach ($item->produk?->komposisi ?? [] as $k) {
                    $kebutuhan[$k->bahan_id] = ($kebutuhan[$k->bahan_id] ?? 0) + $k->jumlah * $item->qty;
                }
            }
        }

        $daftar = Bahan::where('is_aktif', true)->orderBy('jenis')->orderBy('nama')->get()
            ->map(function ($b) use ($kebutuhan) {
                $b->kebutuhan_pesanan = $kebutuhan[$b->id] ?? 0;
                // Cukup untuk pesanan yang menunggu, dan sisanya kembali ke batas minimum
                $b->saran_beli = max(0, $b->stok_minimum + $b->kebutuhan_pesanan - $b->stok);

                return $b;
            })
            ->filter(fn ($b) => $b->saran_beli > 0);

        return view('admin.bahan.daftar-belanja', [
            'daftar' => $daftar,
            'jenis' => self::JENIS,
            'jumlahPesanan' => $pesananMasuk->count(),
        ]);
    }

    public function create()
    {
        return view('admin.bahan.form', ['bahan' => new Bahan(['is_aktif' => true]), 'jenis' => self::JENIS]);
    }

    public function store(BahanRequest $request, MutasiStokService $mutasi)
    {
        $bahan = Bahan::create($request->safe()->except('stok_awal'));

        if ($request->validated('stok_awal') > 0) {
            $mutasi->masuk($bahan, $request->validated('stok_awal'), ['keterangan' => 'Stok awal']);
        }

        return redirect()->route('admin.bahan.index')->with('sukses', "Bahan {$bahan->nama} ditambahkan.");
    }

    public function edit(Bahan $bahan)
    {
        return view('admin.bahan.form', ['bahan' => $bahan, 'jenis' => self::JENIS]);
    }

    public function update(BahanRequest $request, Bahan $bahan)
    {
        $bahan->update($request->safe()->except('stok_awal'));

        return redirect()->route('admin.bahan.index')->with('sukses', "Bahan {$bahan->nama} diperbarui.");
    }

    public function destroy(Bahan $bahan)
    {
        $dipakai = $bahan->produk()->pluck('nama');
        if ($dipakai->isNotEmpty()) {
            return back()->with('gagal', "{$bahan->nama} masih dipakai di komposisi: {$dipakai->join(', ')}. Hapus dari komposisi dulu, atau cukup nonaktifkan bahannya.");
        }

        $bahan->delete();

        return redirect()->route('admin.bahan.index')->with('sukses', "Bahan {$bahan->nama} dihapus.");
    }
}

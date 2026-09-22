<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Stok\MutasiStokService;
use App\Http\Controllers\Controller;
use App\Http\Requests\MutasiStokRequest;
use App\Models\Bahan;
use App\Models\MutasiStok;
use Illuminate\Http\Request;

class MutasiStokController extends Controller
{
    public function index(Request $request)
    {
        $mutasi = MutasiStok::with('bahan', 'pesanan', 'user')
            ->when($request->bahan, fn ($q, $id) => $q->where('bahan_id', $id))
            ->when($request->tipe, fn ($q, $tipe) => $q->where('tipe', $tipe))
            ->latest('tanggal')
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.mutasi-stok.index', [
            'mutasi' => $mutasi,
            'semuaBahan' => Bahan::orderBy('nama')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.mutasi-stok.create', [
            'semuaBahan' => Bahan::where('is_aktif', true)->orderBy('nama')->get(),
            'bahanDipilih' => $request->integer('bahan') ?: null,
        ]);
    }

    public function store(MutasiStokRequest $request, MutasiStokService $service)
    {
        $data = $request->validated();
        $bahan = Bahan::findOrFail($data['bahan_id']);
        $opsi = [
            'tanggal' => $data['tanggal'],
            'keterangan' => $data['keterangan'] ?? null,
        ];

        match ($data['tipe']) {
            'masuk' => $service->masuk($bahan, $data['jumlah'], $opsi + [
                'tanggal_kadaluarsa' => $bahan->jenis === 'fresh' ? ($data['tanggal_kadaluarsa'] ?? null) : null,
                'harga_beli' => $data['harga_beli'] ?? null,
            ]),
            'keluar' => $service->keluar($bahan, $data['jumlah'], $opsi),
            'penyesuaian' => $service->penyesuaian($bahan, $data['jumlah'], $data['keterangan'], $opsi),
        };

        $bahan->refresh();

        return redirect()->route('admin.bahan.index')
            ->with('sukses', "Tercatat. Sisa {$bahan->nama} sekarang ".rtrim(rtrim(number_format((float) $bahan->stok, 2, ',', '.'), '0'), ',')." {$bahan->satuan}.");
    }
}

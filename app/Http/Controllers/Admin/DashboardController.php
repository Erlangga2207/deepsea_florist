<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Jadwal\CekKapasitas;
use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\Pesanan;
use App\Models\StokProdukJadi;

// Dashboard untuk mengerjakan hari ini, bukan melihat ke belakang (grafik ada di Laporan).
class DashboardController extends Controller
{
    public function __invoke(CekKapasitas $kapasitas)
    {
        $antrian = Pesanan::aktif()
            ->with('pelanggan', 'item')
            ->whereDate('tanggal_jadi', '<=', today())
            ->orderBy('tanggal_jadi')
            ->get();

        $bahanMenipis = Bahan::where('is_aktif', true)->orderBy('nama')->get()
            ->filter(fn ($b) => $b->statusStok()[0] !== 'Aman')
            ->sortBy(fn ($b) => $b->stok / max($b->stok_minimum, 1));

        $bebanMinggu = $kapasitas->rentang(today(), 7);

        return view('admin.dashboard', [
            'jumlahAktif' => Pesanan::aktif()->count(),
            'jatuhTempo' => Pesanan::aktif()->whereDate('tanggal_jadi', today())->count(),
            'bebanHariIni' => $bebanMinggu[0],
            'antrian' => $antrian,
            'bahanMenipis' => $bahanMenipis,
            'bebanMinggu' => $bebanMinggu,
            'siapJual' => StokProdukJadi::where('status', 'tersedia')->latest()->get(),
        ]);
    }
}

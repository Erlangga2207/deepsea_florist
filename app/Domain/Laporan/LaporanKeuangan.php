<?php

namespace App\Domain\Laporan;

use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\Pesanan;
use Illuminate\Support\Carbon;

// Laba sederhana: penjualan − pengeluaran, berdasarkan tanggal jadi pesanan.
// Penjualan = total pesanan "selesai" + DP hangus dari pesanan "batal" (docs/07-ACCEPTANCE.md H1).
class LaporanKeuangan
{
    public function untuk(Carbon $dari, Carbon $sampai): array
    {
        $rentang = [$dari->toDateString(), $sampai->toDateString()];

        $selesai = Pesanan::with('pelanggan', 'item')
            ->where('status', 'selesai')
            ->whereBetween('tanggal_jadi', $rentang)
            ->orderBy('tanggal_jadi')
            ->get();

        $batal = Pesanan::with('pelanggan', 'item')
            ->where('status', 'batal')
            ->where('total_dibayar', '>', 0)
            ->whereBetween('tanggal_jadi', $rentang)
            ->orderBy('tanggal_jadi')
            ->get();

        $pengeluaran = Pengeluaran::whereBetween('tanggal', $rentang)->orderBy('tanggal')->get();

        $totalPenjualan = $selesai->sum('total') + $batal->sum('total_dibayar');
        $totalPengeluaran = $pengeluaran->sum('nominal');

        return [
            'dari' => $dari,
            'sampai' => $sampai,
            'selesai' => $selesai,
            'batal' => $batal,
            'pengeluaran' => $pengeluaran,
            'pengeluaranPerKategori' => $pengeluaran->groupBy('kategori')->map->sum('nominal')->sortDesc(),
            'totalPenjualan' => $totalPenjualan,
            'totalPengeluaran' => $totalPengeluaran,
            'laba' => $totalPenjualan - $totalPengeluaran,
            // Arus kas: uang DP & pelunasan yang benar-benar diterima di periode ini
            'uangDiterima' => Pembayaran::whereBetween('tanggal', $rentang)->sum('jumlah'),
        ];
    }
}

<?php

namespace App\Domain\Jadwal;

use App\Models\Pengaturan;
use App\Models\Pesanan;
use Illuminate\Support\Carbon;

// "Masih sanggup dikerjakan di tanggal itu atau tidak?"
class CekKapasitas
{
    public const CARI_SARAN_HARI = 7;

    private float $kapasitas;

    public function __construct()
    {
        $pengaturan = Pengaturan::ambil();
        $this->kapasitas = $pengaturan->jumlah_perakit * $pengaturan->jam_kerja_per_hari;
    }

    public function untukTanggal(Carbon $tanggal, float $tambahanJam = 0): HasilKapasitas
    {
        $beban = $this->beban($tanggal);
        $penuh = ($beban + $tambahanJam) > $this->kapasitas;

        return new HasilKapasitas(
            tanggal: $tanggal->copy()->startOfDay(),
            beban: $beban,
            kapasitas: $this->kapasitas,
            tambahanJam: $tambahanJam,
            penuh: $penuh,
            tanggalSaran: $penuh ? $this->cariTanggal($tanggal, $tambahanJam) : null,
        );
    }

    // Dipakai dashboard untuk batang beban beberapa hari ke depan
    public function rentang(Carbon $mulai, int $hari = 7): array
    {
        return collect(range(0, $hari - 1))
            ->map(fn ($i) => $this->untukTanggal($mulai->copy()->addDays($i)))
            ->all();
    }

    // Σ estimasi jam pesanan aktif (masuk/dikerjakan/jadi) yang jatuh tempo di tanggal itu
    public function beban(Carbon $tanggal): float
    {
        return Pesanan::aktif()
            ->whereDate('tanggal_jadi', $tanggal)
            ->with('item.produk')
            ->get()
            ->sum(fn ($p) => $p->estimasiJam());
    }

    private function cariTanggal(Carbon $tanggal, float $tambahanJam): ?Carbon
    {
        for ($i = 1; $i <= self::CARI_SARAN_HARI; $i++) {
            $calon = $tanggal->copy()->startOfDay()->addDays($i);
            if ($this->beban($calon) + $tambahanJam <= $this->kapasitas) {
                return $calon;
            }
        }

        return null;
    }
}

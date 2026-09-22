<?php

namespace App\Domain\Jadwal;

use Illuminate\Support\Carbon;

class HasilKapasitas
{
    public function __construct(
        public Carbon $tanggal,
        public float $beban,
        public float $kapasitas,
        public float $tambahanJam = 0,
        public bool $penuh = false,
        public ?Carbon $tanggalSaran = null,
    ) {}

    public function persen(): float
    {
        return $this->kapasitas > 0 ? ($this->beban + $this->tambahanJam) / $this->kapasitas * 100 : 100;
    }

    // Warna batang beban: hijau < 85%, amber 85–100%, merah > 100%
    public function warna(): string
    {
        return match (true) {
            $this->persen() > 100 => 'merah',
            $this->persen() >= 85 => 'amber',
            default => 'hijau',
        };
    }

    public function pesan(): string
    {
        $jam = fn ($n) => rtrim(rtrim(number_format($n, 1, ',', '.'), '0'), ',');
        $teks = "Tanggal {$this->tanggal->translatedFormat('j F')} sudah terisi {$jam($this->beban)} dari {$jam($this->kapasitas)} jam";
        if ($this->tambahanJam > 0) {
            $teks .= ", ditambah pesanan ini jadi {$jam($this->beban + $this->tambahanJam)} jam";
        }
        $teks .= '.';

        if ($this->tanggalSaran) {
            $teks .= " Tanggal terdekat yang masih muat: {$this->tanggalSaran->translatedFormat('l, j F')}.";
        }

        return $teks;
    }
}

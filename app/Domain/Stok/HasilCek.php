<?php

namespace App\Domain\Stok;

class HasilCek
{
    public function __construct(
        public bool $layak = true,
        public bool $tanpaKomposisi = false,
        // [['nama', 'satuan', 'kurang'], ...]
        public array $bahanKurang = [],
        // [['jenis' => 'produk'|'stok_jadi', 'produk_id', 'kode', 'nama', 'foto', 'keterangan'], ...]
        public array $saranAlternatif = [],
    ) {}

    // Contoh: "Kurang 6 tangkai mawar putih"
    public function pesan(): string
    {
        if ($this->tanpaKomposisi) {
            return 'Model ini belum punya komposisi, jadi stok bahan tidak dicek.';
        }
        if ($this->layak) {
            return 'Bahan cukup.';
        }

        $daftar = array_map(
            fn ($b) => rtrim(rtrim(number_format($b['kurang'], 2, ',', '.'), '0'), ',').' '.$b['satuan'].' '.mb_strtolower($b['nama']),
            $this->bahanKurang
        );

        return 'Kurang '.implode(', ', $daftar);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bahan extends Model
{
    use SoftDeletes;

    protected $table = 'bahan';

    // 'stok' sengaja tidak bisa diisi massal: stok hanya berubah lewat MutasiStokService.
    protected $guarded = ['id', 'stok'];

    protected function casts(): array
    {
        return [
            'stok' => 'decimal:2',
            'stok_minimum' => 'decimal:2',
            'harga_beli_terakhir' => 'decimal:2',
            'dijual_eceran' => 'boolean',
            'harga_eceran' => 'decimal:2',
            'is_aktif' => 'boolean',
        ];
    }

    public function produk(): BelongsToMany
    {
        return $this->belongsToMany(Produk::class, 'produk_bahan')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    public function mutasi(): HasMany
    {
        return $this->hasMany(MutasiStok::class);
    }

    public function masukTerakhir(): HasOne
    {
        return $this->hasOne(MutasiStok::class)->ofMany(['tanggal' => 'max', 'id' => 'max'], fn ($q) => $q->where('tipe', 'masuk'));
    }

    // Habis bila sisa ≤ ¼ minimum, Menipis bila di bawah minimum.
    // Return [label, warna pill]
    public function statusStok(): array
    {
        $stok = (float) $this->stok;
        $minimum = (float) $this->stok_minimum;

        return match (true) {
            $stok <= 0 || $stok <= $minimum / 4 => ['Habis', 'merah'],
            $stok < $minimum => ['Menipis', 'amber'],
            default => ['Aman', 'hijau'],
        };
    }

    // Untuk kolom "Catatan umur": hanya bahan fresh yang bisa layu.
    public function catatanUmur(): string
    {
        if ($this->jenis === 'artificial') {
            return 'Tidak layu';
        }

        $kadaluarsa = $this->masukTerakhir?->tanggal_kadaluarsa;
        if ($this->jenis !== 'fresh' || ! $kadaluarsa) {
            return '—';
        }

        $sisaHari = (int) today()->diffInDays($kadaluarsa, false);

        return match (true) {
            $sisaHari < 0 => 'Lewat '.abs($sisaHari).' hari',
            $sisaHari === 0 => 'Layu hari ini',
            default => 'Layu dalam '.$sisaHari.' hari',
        };
    }
}

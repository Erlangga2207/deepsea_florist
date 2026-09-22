<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Produk extends Model
{
    use SoftDeletes;

    protected $table = 'produk';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'harga_dasar' => 'decimal:2',
            'tampilkan_harga' => 'boolean',
            'estimasi_jam' => 'decimal:2',
            'faktor_kerumitan' => 'integer',
            'is_aktif' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Produk $produk) {
            $produk->kode ??= static::kodeBerikutnya();
            $produk->slug ??= static::slugUnik($produk->nama, $produk->kode);
        });
    }

    // Nama produk boleh kembar, slug tidak. Kalau sudah dipakai, tempelkan kodenya.
    public static function slugUnik(string $nama, string $kode): string
    {
        $slug = Str::slug($nama);

        return static::withTrashed()->where('slug', $slug)->exists()
            ? $slug.'-'.Str::lower($kode)
            : $slug;
    }

    // Format DF-001, DF-002, ... (produk yang sudah dihapus ikut dihitung supaya kode tidak dipakai ulang)
    public static function kodeBerikutnya(): string
    {
        $terakhir = static::withTrashed()->where('kode', 'like', 'DF-___')->max('kode');
        $nomor = $terakhir ? (int) substr($terakhir, 3) + 1 : 1;

        return 'DF-'.str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function foto(): HasMany
    {
        return $this->hasMany(ProdukFoto::class)->orderBy('urutan');
    }

    // Baris komposisi: $item->jumlah dan $item->bahan
    public function komposisi(): HasMany
    {
        return $this->hasMany(ProdukBahan::class);
    }

    // Bahan langsung, jumlahnya di $bahan->pivot->jumlah
    public function bahan(): BelongsToMany
    {
        return $this->belongsToMany(Bahan::class, 'produk_bahan')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    public function pesananItem(): HasMany
    {
        return $this->hasMany(PesananItem::class);
    }

    public function stokProdukJadi(): HasMany
    {
        return $this->hasMany(StokProdukJadi::class);
    }
}

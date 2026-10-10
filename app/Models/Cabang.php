<?php

namespace App\Models;

use App\Models\Concerns\NomorWa;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

// Hanya data lokasi publik. Stok, pesanan, dan kapasitas tetap terpusat — jangan tambah cabang_id ke tabel lain.
class Cabang extends Model
{
    use NomorWa;

    protected $table = 'cabang';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_utama' => 'boolean',
            'tampil_di_website' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public static function utama(): ?self
    {
        return static::where('is_utama', true)->first();
    }

    public function scopeTampil($query)
    {
        return $query->where('tampil_di_website', true);
    }

    public function scopeUrut($query)
    {
        return $query->orderByDesc('is_utama')->orderBy('urutan')->orderBy('id');
    }

    public function koordinat(): string
    {
        return (float) $this->latitude.','.(float) $this->longitude;
    }

    protected function embedUrl(): Attribute
    {
        return Attribute::get(fn () => 'https://maps.google.com/maps?q='.$this->koordinat().'&output=embed');
    }

    protected function arahUrl(): Attribute
    {
        return Attribute::get(fn () => 'https://www.google.com/maps/dir/?api=1&destination='.$this->koordinat());
    }
}

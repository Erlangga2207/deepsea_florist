<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokProdukJadi extends Model
{
    protected $table = 'stok_produk_jadi';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['harga_jual' => 'decimal:2'];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class)->withTrashed();
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }
}

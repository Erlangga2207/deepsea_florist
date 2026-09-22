<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProdukBahan extends Model
{
    protected $table = 'produk_bahan';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['jumlah' => 'decimal:2'];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $guarded = ['id'];

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class);
    }
}

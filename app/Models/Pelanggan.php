<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $guarded = ['id'];

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }
}

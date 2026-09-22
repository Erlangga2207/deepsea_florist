<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';

    protected $guarded = ['id'];

    public const KATEGORI = [
        'bahan' => 'Bahan',
        'gaji' => 'Gaji',
        'sewa' => 'Sewa',
        'listrik' => 'Listrik',
        'wifi' => 'Wi-Fi',
        'lain' => 'Lain-lain',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'nominal' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mutasiStok(): HasMany
    {
        return $this->hasMany(MutasiStok::class);
    }
}

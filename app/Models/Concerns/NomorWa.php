<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

// Dipakai Pengaturan dan Cabang: no_wa selalu disimpan 62xxxx supaya link wa.me tidak rusak.
trait NomorWa
{
    // "0812-9683 6363" / "+62 812…" / "62812…" → "62812…"
    public static function normalisasiWa(?string $nomor): ?string
    {
        $wa = preg_replace('/\D/', '', (string) $nomor);
        if ($wa === '') {
            return null;
        }

        return str_starts_with($wa, '0') ? '62'.substr($wa, 1) : $wa;
    }

    protected function noWa(): Attribute
    {
        return Attribute::set(fn (?string $nilai) => static::normalisasiWa($nilai));
    }
}

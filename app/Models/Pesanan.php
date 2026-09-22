<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tanggal_pesan' => 'date',
            'tanggal_jadi' => 'date',
            'subtotal' => 'decimal:2',
            'biaya_tambahan' => 'decimal:2',
            'ongkir' => 'decimal:2',
            'total' => 'decimal:2',
            'total_dibayar' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Pesanan $pesanan) {
            $pesanan->tanggal_pesan ??= now();
            $pesanan->kode ??= static::kodeBerikutnya($pesanan->tanggal_pesan);
        });
    }

    // Format DF-YYMM-NNN, nomor urut mulai lagi dari 001 tiap bulan.
    // ponytail: tanpa kunci baris; dua kasir menyimpan di detik yang sama bisa bentrok (ditahan unique index). Pakai lockForUpdate bila itu terjadi.
    public static function kodeBerikutnya($tanggal): string
    {
        $awalan = 'DF-'.Carbon::parse($tanggal)->format('ym').'-';
        $terakhir = static::where('kode', 'like', $awalan.'%')->max('kode');
        $nomor = $terakhir ? (int) substr($terakhir, -3) + 1 : 1;

        return $awalan.str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    public const STATUS = [
        'masuk' => ['Masuk', 'rose'],
        'dikerjakan' => ['Dikerjakan', 'amber'],
        'jadi' => ['Jadi', 'hijau'],
        'selesai' => ['Selesai', 'neutral'],
        'batal' => ['Batal', 'merah'],
    ];

    // Urutan maju. "batal" di luar urutan.
    public const ALUR = ['masuk', 'dikerjakan', 'jadi', 'selesai'];

    public const STATUS_AKTIF = ['masuk', 'dikerjakan', 'jadi'];

    public function scopeAktif($query)
    {
        return $query->whereIn('status', self::STATUS_AKTIF);
    }

    public function labelStatus(): string
    {
        return self::STATUS[$this->status][0];
    }

    public function warnaStatus(): string
    {
        return self::STATUS[$this->status][1];
    }

    // Status pembayaran sengaja dipisah dari status pengerjaan. Return [label, warna]
    public function statusBayar(): array
    {
        return match (true) {
            $this->status === 'batal' && $this->total_dibayar > 0 => ['DP hangus', 'neutral'],
            $this->total_dibayar >= $this->total => ['Lunas', 'hijau'],
            $this->total_dibayar > 0 => ['DP', 'amber'],
            default => ['Belum bayar', 'merah'],
        };
    }

    // "Hari ini" (merah), "Besok" (amber), selebihnya "Sen, 21 Sep". Return [teks, kelas CSS]
    public function tanggalJadiRelatif(): array
    {
        $tanggal = $this->tanggal_jadi;
        $aktif = in_array($this->status, self::STATUS_AKTIF);

        return match (true) {
            $tanggal->isToday() => ['Hari ini', 'teks-merah'],
            $tanggal->isTomorrow() => ['Besok', 'teks-amber'],
            $aktif && $tanggal->isPast() => ['Lewat · '.$tanggal->translatedFormat('D, j M'), 'teks-merah'],
            default => [$tanggal->translatedFormat('D, j M'), ''],
        };
    }

    public function sisaTagihan(): float
    {
        return max(0, $this->total - $this->total_dibayar);
    }

    public function hitungUlangTotal(): void
    {
        $this->subtotal = $this->item()->sum('subtotal');
        $this->total = $this->subtotal + $this->biaya_tambahan + $this->ongkir;
        $this->save();
    }

    // Jam kerja pesanan ini: Σ estimasi_jam produk × qty. Item custom dihitung 0.
    public function estimasiJam(): float
    {
        return $this->item->sum(fn ($item) => (float) ($item->produk?->estimasi_jam ?? 0) * $item->qty);
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): HasMany
    {
        return $this->hasMany(PesananItem::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function mutasiStok(): HasMany
    {
        return $this->hasMany(MutasiStok::class);
    }

    public function stokProdukJadi(): HasMany
    {
        return $this->hasMany(StokProdukJadi::class);
    }
}

<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

// Salinan WebP ≤ 1200 px di samping file asli (produk/abc.jpg → produk/abc.webp). Pakai GD bawaan PHP.
class FotoWebp
{
    public const SISI_MAKS = 1200;

    public static function pathUntuk(string $path): string
    {
        return preg_replace('/\.[^.\/]+$/', '', $path).'.webp';
    }

    public static function buat(string $path): ?string
    {
        $disk = Storage::disk('public');
        if (! function_exists('imagewebp') || str_ends_with($path, '.webp') || ! $disk->exists($path)) {
            return null;
        }

        $gambar = @imagecreatefromstring($disk->get($path));
        if (! $gambar) {
            return null;
        }

        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);
        $skala = min(1, self::SISI_MAKS / max($lebar, $tinggi));
        if ($skala < 1) {
            $gambar = imagescale($gambar, (int) round($lebar * $skala), (int) round($tinggi * $skala));
        }

        ob_start();
        imagewebp($gambar, null, 80);
        $disk->put($tujuan = self::pathUntuk($path), ob_get_clean());
        imagedestroy($gambar);

        return $tujuan;
    }

    public static function hapus(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete([$path, self::pathUntuk($path)]);
        }
    }
}

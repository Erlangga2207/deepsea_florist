<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use App\Models\Bahan;
use App\Models\Cabang;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if ($this->app->isLocal() && config('app.tanggal_demo')) {
            Carbon::setTestNow(Carbon::parse(config('app.tanggal_demo').' '.date('H:i:s')));
            // Tanpa ini cookie sesi diberi tanggal kedaluwarsa "di masa lalu" dan langsung dibuang browser
            config(['session.expire_on_close' => true]);
        }

        // Data toko tersedia di semua halaman publik tanpa dikirim dari tiap controller
        View::composer(['layouts.publik', 'publik.*', 'components.seo', 'components.kartu-produk'], function ($view) {
            static $pengaturan, $cabang;
            $cabang ??= Cabang::tampil()->urut()->get();
            $view->with([
                'pengaturan' => $pengaturan ??= Pengaturan::ambil(),
                'cabang' => $cabang,
                'cabangUtama' => $cabang->firstWhere('is_utama', true),
            ]);
        });

        // Menu admin dari config/admin-menu.php, sudah disaring per role. Grup kosong ikut dibuang.
        View::composer('layouts.admin', function ($view) {
            $role = auth()->user()->role;
            $badge = [
                'pesanan_masuk' => Pesanan::where('status', 'masuk')->count(),
                'bahan_menipis' => Bahan::menipis()->count(),
            ];

            $menu = collect(config('admin-menu'))
                ->map(function ($grup) use ($role, $badge) {
                    $grup['item'] = collect($grup['item'])
                        ->filter(fn ($i) => Route::has($i['route']) && in_array($i['role'] ?? null, [null, $role]))
                        ->map(fn ($i) => $i + ['jumlah' => $badge[$i['badge'] ?? ''] ?? 0])
                        ->values();

                    return $grup;
                })
                ->filter(fn ($grup) => $grup['item']->isNotEmpty());

            $view->with(['menuAdmin' => $menu, 'badgeAdmin' => $badge]);
        });

        // @rupiah(158000) → Rp 158.000
        Blade::directive('rupiah', fn ($nilai) => "<?php echo 'Rp '.number_format((float) ($nilai), 0, ',', '.'); ?>");

        // @angka(1.50) → 1,5 ; @angka(36.00) → 36
        Blade::directive('angka', fn ($nilai) => "<?php echo rtrim(rtrim(number_format((float) ($nilai), 2, ',', '.'), '0'), ','); ?>");
    }
}

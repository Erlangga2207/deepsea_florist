<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Kategori;

class TentangController extends Controller
{
    public function __invoke()
    {
        return view('publik.tentang', [
            'kategori' => Kategori::orderBy('urutan')
                ->withCount(['produk' => fn ($q) => $q->where('is_aktif', true)])
                ->get(),
        ]);
    }
}

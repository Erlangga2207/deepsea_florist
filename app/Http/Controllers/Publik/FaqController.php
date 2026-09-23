<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function __invoke()
    {
        return view('publik.faq', [
            'faq' => Faq::where('is_aktif', true)->orderBy('urutan')->orderBy('id')->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;

class KontakController extends Controller
{
    public function __invoke()
    {
        return view('publik.kontak');
    }
}

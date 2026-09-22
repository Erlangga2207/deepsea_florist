<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Laporan\LaporanKeuangan;
use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanRequest;
use App\Models\Pengaturan;
use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(LaporanRequest $request, LaporanKeuangan $laporan)
    {
        return view('admin.laporan.index', $laporan->untuk($request->dari(), $request->sampai()) + [
            'kategori' => Pengeluaran::KATEGORI,
        ]);
    }

    public function cetak(LaporanRequest $request, LaporanKeuangan $laporan)
    {
        $data = $laporan->untuk($request->dari(), $request->sampai()) + [
            'kategori' => Pengeluaran::KATEGORI,
            'pengaturan' => Pengaturan::ambil(),
        ];

        $nama = 'laporan-'.$data['dari']->toDateString().'-sd-'.$data['sampai']->toDateString().'.pdf';

        // Subsetting: hanya huruf yang dipakai yang ditanam, supaya file tidak ratusan KB
        return Pdf::setOption('isFontSubsettingEnabled', true)
            ->loadView('admin.laporan.pdf', $data)
            ->setPaper('a4')
            ->download($nama);
    }
}

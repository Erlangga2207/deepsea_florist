<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Laporan\LaporanExcel;
use App\Domain\Laporan\LaporanKeuangan;
use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanRequest;
use App\Models\Cabang;
use App\Models\Pengaturan;
use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            'cabangUtama' => Cabang::utama(),
        ];

        $nama = 'laporan-'.$data['dari']->toDateString().'-sd-'.$data['sampai']->toDateString().'.pdf';

        // Subsetting: hanya huruf yang dipakai yang ditanam, supaya file tidak ratusan KB
        return Pdf::setOption('isFontSubsettingEnabled', true)
            ->loadView('admin.laporan.pdf', $data)
            ->setPaper('a4')
            ->download($nama);
    }

    public function excel(LaporanRequest $request, LaporanKeuangan $laporan, LaporanExcel $excel)
    {
        $data = $laporan->untuk($request->dari(), $request->sampai()) + [
            'kategori' => Pengeluaran::KATEGORI,
        ];

        $nama = 'laporan-'.$data['dari']->toDateString().'-sd-'.$data['sampai']->toDateString().'.xlsx';
        $buku = $excel->buat($data);

        return response()->streamDownload(fn () => (new Xlsx($buku))->save('php://output'), $nama, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

<?php

namespace App\Domain\Laporan;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// Isi sama dengan PDF laporan, dipecah jadi tiga sheet supaya bisa diolah di Excel.
class LaporanExcel
{
    private const RUPIAH = '"Rp" #,##0';

    public function buat(array $data): Spreadsheet
    {
        $buku = new Spreadsheet;

        $ringkasan = $buku->getActiveSheet()->setTitle('Ringkasan');
        $ringkasan->fromArray([
            ['Laporan Keuangan'],
            ['Periode', $data['dari']->translatedFormat('j F Y').' s/d '.$data['sampai']->translatedFormat('j F Y')],
            [],
            ['Penjualan (pesanan selesai + DP hangus)', $data['totalPenjualan']],
            ['Pengeluaran', $data['totalPengeluaran']],
            ['Laba sederhana', $data['laba']],
            ['Uang DP & pelunasan diterima', $data['uangDiterima']],
        ]);
        $ringkasan->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $ringkasan->getStyle('A6:B6')->getFont()->setBold(true);
        $ringkasan->getStyle('B4:B7')->getNumberFormat()->setFormatCode(self::RUPIAH);

        $baris = [];
        foreach ($data['selesai'] as $p) {
            $baris[] = [$p->tanggal_jadi->format('d/m/Y'), $p->kode, $p->pelanggan->nama, $p->item->first()?->nama_item, (float) $p->total];
        }
        foreach ($data['batal'] as $p) {
            $baris[] = [$p->tanggal_jadi->format('d/m/Y'), $p->kode, $p->pelanggan->nama, 'DP hangus (batal)', (float) $p->total_dibayar];
        }
        $this->tabel($buku->createSheet()->setTitle('Penjualan'),
            ['Tanggal jadi', 'Kode', 'Pemesan', 'Keterangan', 'Nilai'], $baris, 'Total penjualan');

        $baris = $data['pengeluaran']->map(fn ($p) => [
            $p->tanggal->format('d/m/Y'), $data['kategori'][$p->kategori], $p->keterangan, (float) $p->nominal,
        ])->all();
        $this->tabel($buku->createSheet()->setTitle('Pengeluaran'),
            ['Tanggal', 'Kategori', 'Keterangan', 'Nominal'], $baris, 'Total pengeluaran');

        foreach ($buku->getAllSheets() as $sheet) {
            foreach (range('A', $sheet->getHighestColumn()) as $kolom) {
                $sheet->getColumnDimension($kolom)->setAutoSize(true);
            }
        }

        return $buku;
    }

    // Header tebal, isi, lalu baris total memakai rumus SUM supaya tetap benar kalau isinya diedit di Excel.
    private function tabel(Worksheet $sheet, array $header, array $baris, string $labelTotal): void
    {
        $kolomNilai = chr(ord('A') + count($header) - 1);
        $akhir = count($baris) + 1;

        $sheet->fromArray([$header, ...$baris]);
        $sheet->getStyle("A1:{$kolomNilai}1")->getFont()->setBold(true);

        $total = $akhir + 1;
        $sheet->setCellValue("A{$total}", $labelTotal);
        $sheet->setCellValue("{$kolomNilai}{$total}", $baris ? "=SUM({$kolomNilai}2:{$kolomNilai}{$akhir})" : 0);
        $sheet->getStyle("A{$total}:{$kolomNilai}{$total}")->getFont()->setBold(true);
        $sheet->getStyle("{$kolomNilai}2:{$kolomNilai}{$total}")->getNumberFormat()->setFormatCode(self::RUPIAH);
    }
}

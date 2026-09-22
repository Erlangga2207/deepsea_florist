<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan {{ $pengaturan->nama_toko }}</title>
    {{-- dompdf tidak membaca CSS variable, jadi warnanya ditulis langsung di sini (hanya untuk PDF) --}}
    <style>
        @page { margin: 28px 36px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #2B2226; }
        .kop { border-bottom: 2px solid #B23A57; padding-bottom: 8px; margin-bottom: 14px; }
        .kop h1 { font-size: 18px; margin: 0; color: #8E2B44; }
        .kop div { color: #6B5C61; }
        h2 { font-size: 13px; margin: 18px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px 6px; border-bottom: 1px solid #E9DEDB; text-align: left; }
        th { font-size: 10px; text-transform: uppercase; color: #6B5C61; }
        .kanan { text-align: right; }
        tfoot td { font-weight: bold; border-top: 1px solid #2B2226; }
        .ringkas td { font-size: 13px; }
        .kecil { color: #6B5C61; font-size: 9px; margin-top: 18px; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>{{ $pengaturan->nama_toko }}</h1>
        <div>{{ $pengaturan->alamat }} · WA {{ preg_replace('/^62/', '0', $pengaturan->no_wa) }}</div>
    </div>

    <strong style="font-size:14px">Laporan Keuangan</strong><br>
    Periode {{ $dari->translatedFormat('j F Y') }} – {{ $sampai->translatedFormat('j F Y') }}

    <h2>Ringkasan</h2>
    <table class="ringkas">
        <tr><td>Penjualan (pesanan selesai + DP hangus)</td><td class="kanan">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td></tr>
        <tr><td>Pengeluaran</td><td class="kanan">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td></tr>
        <tr><td><strong>Laba sederhana</strong></td><td class="kanan"><strong>Rp {{ number_format($laba, 0, ',', '.') }}</strong></td></tr>
    </table>

    <h2>Penjualan</h2>
    <table>
        <thead><tr><th>Tanggal jadi</th><th>Kode</th><th>Pemesan</th><th>Keterangan</th><th class="kanan">Nilai</th></tr></thead>
        <tbody>
        @foreach ($selesai as $p)
            <tr><td>{{ $p->tanggal_jadi->format('d/m/Y') }}</td><td>{{ $p->kode }}</td><td>{{ $p->pelanggan->nama }}</td><td>{{ $p->item->first()?->nama_item }}</td><td class="kanan">{{ number_format($p->total, 0, ',', '.') }}</td></tr>
        @endforeach
        @foreach ($batal as $p)
            <tr><td>{{ $p->tanggal_jadi->format('d/m/Y') }}</td><td>{{ $p->kode }}</td><td>{{ $p->pelanggan->nama }}</td><td>DP hangus (batal)</td><td class="kanan">{{ number_format($p->total_dibayar, 0, ',', '.') }}</td></tr>
        @endforeach
        @if ($selesai->isEmpty() && $batal->isEmpty())
            <tr><td colspan="5">Belum ada penjualan di periode ini.</td></tr>
        @endif
        </tbody>
        <tfoot><tr><td colspan="4">Total penjualan</td><td class="kanan">{{ number_format($totalPenjualan, 0, ',', '.') }}</td></tr></tfoot>
    </table>

    <h2>Pengeluaran</h2>
    <table>
        <thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th class="kanan">Nominal</th></tr></thead>
        <tbody>
        @forelse ($pengeluaran as $p)
            <tr><td>{{ $p->tanggal->format('d/m/Y') }}</td><td>{{ $kategori[$p->kategori] }}</td><td>{{ $p->keterangan }}</td><td class="kanan">{{ number_format($p->nominal, 0, ',', '.') }}</td></tr>
        @empty
            <tr><td colspan="4">Belum ada pengeluaran di periode ini.</td></tr>
        @endforelse
        </tbody>
        <tfoot><tr><td colspan="3">Total pengeluaran</td><td class="kanan">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td></tr></tfoot>
    </table>

    <div class="kecil">Dicetak {{ now()->translatedFormat('j F Y H.i') }}. Uang DP &amp; pelunasan yang diterima di periode ini: Rp {{ number_format($uangDiterima, 0, ',', '.') }}.</div>
</body>
</html>

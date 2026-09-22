@extends('layouts.admin')

@section('judul', 'Laporan')

@section('konten')
    <div class="judul-halaman">
        <div>
            <h1>Laporan</h1>
            <div class="text-ink-2">{{ $dari->translatedFormat('j F Y') }} – {{ $sampai->translatedFormat('j F Y') }}</div>
        </div>
        <a href="{{ route('admin.laporan.cetak', ['dari' => $dari->toDateString(), 'sampai' => $sampai->toDateString()]) }}" class="btn btn-primary">Cetak PDF</a>
    </div>

    <form method="GET" class="d-flex flex-wrap align-items-end gap-2 mb-4">
        <div>
            <label for="dari" class="form-label small">Dari</label>
            <input type="date" id="dari" name="dari" value="{{ $dari->toDateString() }}" class="form-control">
        </div>
        <div>
            <label for="sampai" class="form-label small">Sampai</label>
            <input type="date" id="sampai" name="sampai" value="{{ $sampai->toDateString() }}" class="form-control">
        </div>
        <button class="btn btn-garis">Tampilkan</button>
    </form>
    @error('sampai') <div class="alert alert-danger">{{ $message }}</div> @enderror

    <div class="row g-3 mb-4">
        <div class="col-md-4"><x-tile label="Penjualan" :nilai="'Rp '.number_format($totalPenjualan, 0, ',', '.')" sub="pesanan selesai + DP hangus" /></div>
        <div class="col-md-4"><x-tile label="Pengeluaran" :nilai="'Rp '.number_format($totalPengeluaran, 0, ',', '.')" sub="semua kategori" /></div>
        <div class="col-md-4"><x-tile label="Laba sederhana" :nilai="'Rp '.number_format($laba, 0, ',', '.')" sub="penjualan − pengeluaran" :nada="$laba < 0 ? 'merah' : null" /></div>
    </div>
    <p class="small text-ink-2 mb-4">
        Penjualan dihitung dari tanggal jadi pesanan. Uang yang benar-benar diterima (DP &amp; pelunasan) di periode ini: <strong class="text-body">@rupiah($uangDiterima)</strong>.
    </p>

    <div class="row g-4">
        <div class="col-lg-7">
            <x-panel judul="Penjualan">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Tanggal jadi</th><th>Kode</th><th>Pemesan</th><th>Keterangan</th><th class="angka">Nilai</th></tr></thead>
                        <tbody>
                        @foreach ($selesai as $p)
                            <tr>
                                <td class="text-nowrap">{{ $p->tanggal_jadi->translatedFormat('j M') }}</td>
                                <td><a href="{{ route('admin.pesanan.show', $p) }}" class="angka">{{ $p->kode }}</a></td>
                                <td>{{ $p->pelanggan->nama }}</td>
                                <td>{{ $p->item->first()?->nama_item }}</td>
                                <td class="angka text-nowrap">@rupiah($p->total)</td>
                            </tr>
                        @endforeach
                        @foreach ($batal as $p)
                            <tr>
                                <td class="text-nowrap">{{ $p->tanggal_jadi->translatedFormat('j M') }}</td>
                                <td><a href="{{ route('admin.pesanan.show', $p) }}" class="angka">{{ $p->kode }}</a></td>
                                <td>{{ $p->pelanggan->nama }}</td>
                                <td>DP hangus (batal)</td>
                                <td class="angka text-nowrap">@rupiah($p->total_dibayar)</td>
                            </tr>
                        @endforeach
                        @if ($selesai->isEmpty() && $batal->isEmpty())
                            <tr><td colspan="5" class="text-center text-ink-2 py-4">Belum ada penjualan di periode ini.</td></tr>
                        @endif
                        </tbody>
                        <tfoot><tr class="fw-bold"><td colspan="4">Total penjualan</td><td class="angka text-nowrap">@rupiah($totalPenjualan)</td></tr></tfoot>
                    </table>
                </div>
            </x-panel>
        </div>
        <div class="col-lg-5">
            <x-panel judul="Pengeluaran per kategori">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                        @forelse ($pengeluaranPerKategori as $k => $nominal)
                            <tr><td>{{ $kategori[$k] }}</td><td class="angka text-nowrap">@rupiah($nominal)</td></tr>
                        @empty
                            <tr><td class="text-center text-ink-2 py-4">Belum ada pengeluaran.</td></tr>
                        @endforelse
                        </tbody>
                        <tfoot><tr class="fw-bold"><td>Total pengeluaran</td><td class="angka text-nowrap">@rupiah($totalPengeluaran)</td></tr></tfoot>
                    </table>
                </div>
            </x-panel>
        </div>
    </div>
@endsection

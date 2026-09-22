@extends('layouts.admin')

@section('judul', 'Daftar belanja')

@section('konten')
    <div class="judul-halaman">
        <div>
            <h1>Daftar belanja</h1>
            <div class="text-ink-2">{{ today()->translatedFormat('l, j F Y') }} · memperhitungkan {{ $jumlahPesanan }} pesanan yang belum dikerjakan</div>
        </div>
        <button type="button" class="btn btn-garis no-print" onclick="window.print()">Cetak</button>
    </div>

    <x-panel>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Bahan</th>
                    <th class="angka">Sisa</th>
                    <th class="angka">Minimum</th>
                    <th class="angka">Untuk pesanan</th>
                    <th class="angka">Beli</th>
                    <th class="no-print">Harga terakhir</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($daftar as $b)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $b->nama }}</div>
                            <div class="small text-ink-2">{{ $jenis[$b->jenis] }}</div>
                        </td>
                        <td class="angka text-nowrap">@angka($b->stok)</td>
                        <td class="angka text-nowrap">@angka($b->stok_minimum)</td>
                        <td class="angka text-nowrap">@if ($b->kebutuhan_pesanan) @angka($b->kebutuhan_pesanan) @else — @endif</td>
                        <td class="angka text-nowrap fw-bold">@angka($b->saran_beli) {{ $b->satuan }}</td>
                        <td class="no-print text-nowrap text-ink-2">@rupiah($b->harga_beli_terakhir)/{{ $b->satuan }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-ink-2 py-4">Tidak ada yang perlu dibeli. Semua bahan cukup.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-panel>
    <p class="small text-ink-2">Jumlah beli = minimum + kebutuhan pesanan yang belum dikerjakan − sisa sekarang.</p>
@endsection

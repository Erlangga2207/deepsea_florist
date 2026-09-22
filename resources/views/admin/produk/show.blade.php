@extends('layouts.admin')

@section('judul', $produk->nama)

@section('konten')
    <div class="judul-halaman">
        <div>
            <h1>{{ $produk->nama }}</h1>
            <div class="text-ink-2">{{ $produk->kode }} · {{ $produk->kategori->nama }}</div>
        </div>
        <a href="{{ route('admin.produk.edit', $produk) }}" class="btn btn-garis">Ubah produk</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <x-panel judul="Komposisi bahan (1 buket)">
                @if ($produk->komposisi->isEmpty())
                    <p class="text-ink-2 mb-0">
                        Belum ada komposisi, jadi modal bahan dihitung Rp 0 dan harga diisi manual saat pesanan.
                        <a href="{{ route('admin.produk.edit', $produk) }}">Isi komposisi</a>.
                    </p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Bahan</th>
                                <th class="angka">Jumlah</th>
                                <th class="angka">Harga beli</th>
                                <th class="angka">Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($produk->komposisi as $item)
                                <tr>
                                    <td>{{ $item->bahan->nama }}</td>
                                    <td class="angka text-nowrap">@angka($item->jumlah) {{ $item->bahan->satuan }}</td>
                                    <td class="angka text-nowrap">@rupiah($item->bahan->harga_beli_terakhir)</td>
                                    <td class="angka text-nowrap">@rupiah($item->jumlah * $item->bahan->harga_beli_terakhir)</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="3">Modal bahan</th>
                                <th class="angka text-nowrap">@rupiah($rincian['modal_bahan'])</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </x-panel>

            <x-panel judul="Data pengerjaan">
                <dl class="row mb-0">
                    <dt class="col-sm-5 fw-normal text-ink-2">Estimasi pengerjaan</dt>
                    <dd class="col-sm-7">@angka($produk->estimasi_jam) jam per buket</dd>
                    <dt class="col-sm-5 fw-normal text-ink-2">Tingkat kerumitan</dt>
                    <dd class="col-sm-7">{{ $produk->faktor_kerumitan }} dari 3</dd>
                    <dt class="col-sm-5 fw-normal text-ink-2">Harga dasar (patokan)</dt>
                    <dd class="col-sm-7 mb-0">@if ($produk->harga_dasar) @rupiah($produk->harga_dasar) @else — @endif</dd>
                </dl>
            </x-panel>
        </div>

        <div class="col-lg-5">
            <x-panel judul="Perkiraan harga">
                <p class="small text-ink-2 mb-3">
                    Rumus {{ $rincian['label'] }}: <strong class="text-body">{{ $rincian['rumus'] }}</strong>
                    <br>Dihitung oleh kelas <code>{{ class_basename($kalkulator) }}</code>.
                </p>

                @if (in_array($produk->kategori->tipe_harga, ['uang', 'papan']))
                    <form method="GET" class="row g-2 mb-3">
                        @if ($produk->kategori->tipe_harga === 'uang')
                            <div class="col-6">
                                <label class="form-label small" for="jumlah_lembar">Jumlah lembar</label>
                                <input type="number" min="0" id="jumlah_lembar" name="jumlah_lembar" value="{{ $opsi['jumlah_lembar'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label small" for="tarif_lipat">Tarif lipat / lembar</label>
                                <input type="number" min="0" id="tarif_lipat" name="tarif_lipat" value="{{ $opsi['tarif_lipat'] ?? '' }}" class="form-control">
                            </div>
                        @else
                            <div class="col-6">
                                <label class="form-label small" for="sewa_rangka">Sewa rangka</label>
                                <input type="number" min="0" id="sewa_rangka" name="sewa_rangka" value="{{ $opsi['sewa_rangka'] ?? '' }}" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label small" for="ongkos_pasang">Ongkos pasang</label>
                                <input type="number" min="0" id="ongkos_pasang" name="ongkos_pasang" value="{{ $opsi['ongkos_pasang'] ?? '' }}" class="form-control">
                            </div>
                        @endif
                        <div class="col-12"><button class="btn btn-garis btn-sm">Hitung ulang</button></div>
                    </form>
                @endif

                <table class="table table-sm">
                    <tbody>
                    <tr>
                        <td>Modal bahan</td>
                        <td class="angka">@rupiah($rincian['modal_bahan'])</td>
                    </tr>
                    @if (str_contains($rincian['rumus'], 'margin'))
                        <tr>
                            <td>× margin @angka($rincian['margin'])</td>
                            <td class="angka">@rupiah($rincian['modal_bahan'] * $rincian['margin'])</td>
                        </tr>
                    @endif
                    @foreach ($rincian['tambahan'] as $label => $nilai)
                        <tr>
                            <td>{{ $label }}</td>
                            <td class="angka">@rupiah($nilai)</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>
                            Ongkos jasa
                            <div class="small text-ink-2">@angka($produk->estimasi_jam) jam × tarif × kerumitan {{ $produk->faktor_kerumitan }}</div>
                        </td>
                        <td class="angka">@rupiah($rincian['ongkos_jasa'])</td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Saran harga</td>
                        <td class="angka">@rupiah($rincian['saran_harga'])</td>
                    </tr>
                    </tbody>
                </table>

                <p class="small text-ink-2 mb-0">
                    Ini saran, bukan harga mati. Margin dan tarif jasa diatur pemilik di menu Pengaturan.
                </p>
            </x-panel>
        </div>
    </div>
@endsection

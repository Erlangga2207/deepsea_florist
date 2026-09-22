@extends('layouts.admin')

@section('judul', $pesanan->kode)

@section('konten')
    @php
        $item = $pesanan->item->first();
        [$bayarLabel, $bayarWarna] = $pesanan->statusBayar();
        [$tanggalTeks, $tanggalKelas] = $pesanan->tanggalJadiRelatif();
        $isOwner = auth()->user()->role === 'owner';
        $posisi = array_search($pesanan->status, \App\Models\Pesanan::ALUR);
        $berikutnya = $posisi !== false ? (\App\Models\Pesanan::ALUR[$posisi + 1] ?? null) : null;
        $sebelumnya = $posisi ? array_slice(\App\Models\Pesanan::ALUR, 0, $posisi) : [];
        $bisaBayar = $pesanan->status !== 'batal' && $pesanan->sisaTagihan() > 0 && $pesanan->pembayaran->count() < 2;
    @endphp

    <div class="judul-halaman">
        <div>
            <h1 class="angka">{{ $pesanan->kode }}</h1>
            <div class="d-flex flex-wrap gap-2 mt-1">
                <x-pill :warna="$pesanan->warnaStatus()" :label="$pesanan->labelStatus()" />
                <x-pill :warna="$bayarWarna" :label="$bayarLabel" />
            </div>
        </div>
        <a href="{{ route('admin.pesanan.edit', $pesanan) }}" class="btn btn-garis">Ubah data</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <x-panel judul="Pesanan">
                <dl class="row mb-0">
                    <dt class="col-sm-4 fw-normal text-ink-2">Model</dt>
                    <dd class="col-sm-8">
                        {{ $item->nama_item }} × {{ $item->qty }}
                        @if ($item->produk) <span class="text-ink-2">({{ $item->produk->kode }})</span> @else <span class="text-ink-2">(custom)</span> @endif
                    </dd>
                    <dt class="col-sm-4 fw-normal text-ink-2">Ukuran · warna</dt>
                    <dd class="col-sm-8">{{ $item->ukuran ?: '—' }} · {{ $item->warna ?: '—' }}</dd>
                    <dt class="col-sm-4 fw-normal text-ink-2">Kartu ucapan</dt>
                    <dd class="col-sm-8">{{ $item->kartu_ucapan ?: '—' }}</dd>
                    <dt class="col-sm-4 fw-normal text-ink-2">Tanggal jadi</dt>
                    <dd class="col-sm-8"><span class="{{ $tanggalKelas }}">{{ $tanggalTeks }}</span> · {{ $pesanan->tanggal_jadi->translatedFormat('j F Y') }}</dd>
                    <dt class="col-sm-4 fw-normal text-ink-2">Pengambilan</dt>
                    <dd class="col-sm-8">{{ $pesanan->metode_ambil === 'antar' ? 'Diantar ke '.($pesanan->alamat_antar ?: '(alamat belum diisi)') : 'Diambil di toko' }}</dd>
                    <dt class="col-sm-4 fw-normal text-ink-2">Catatan</dt>
                    <dd class="col-sm-8">{{ $pesanan->catatan ?: '—' }}</dd>
                </dl>
            </x-panel>

            <x-panel judul="Pemesan">
                <dl class="row mb-0">
                    <dt class="col-sm-4 fw-normal text-ink-2">Nama</dt>
                    <dd class="col-sm-8">{{ $pesanan->pelanggan->nama }}</dd>
                    <dt class="col-sm-4 fw-normal text-ink-2">Kontak</dt>
                    <dd class="col-sm-8">{{ $pesanan->pelanggan->kontak ?: '—' }}</dd>
                    <dt class="col-sm-4 fw-normal text-ink-2">Sumber</dt>
                    <dd class="col-sm-8">{{ $sumber[$pesanan->pelanggan->sumber] }}</dd>
                    <dt class="col-sm-4 fw-normal text-ink-2">Dicatat</dt>
                    <dd class="col-sm-8 mb-0">{{ $pesanan->tanggal_pesan->translatedFormat('j M Y') }} oleh {{ $pesanan->user->name }}</dd>
                </dl>
            </x-panel>

            @if ($pesanan->mutasiStok->isNotEmpty())
                <x-panel judul="Bahan yang sudah diambil dari stok">
                    <ul class="mb-0">
                        @foreach ($pesanan->mutasiStok as $m)
                            <li>@angka($m->jumlah) {{ $m->bahan->satuan }} {{ $m->bahan->nama }}</li>
                        @endforeach
                    </ul>
                </x-panel>
            @endif
        </div>

        <div class="col-lg-5">
            <x-panel judul="Status pengerjaan">
                @if ($berikutnya)
                    <form method="POST" action="{{ route('admin.pesanan.status', $pesanan) }}" class="mb-3">
                        @csrf
                        <input type="hidden" name="status" value="{{ $berikutnya }}">
                        <button class="btn btn-primary w-100">Tandai {{ strtolower(\App\Models\Pesanan::STATUS[$berikutnya][0]) }}</button>
                        @if ($berikutnya === 'dikerjakan')
                            <div class="form-text">Bahan sesuai komposisi akan diambil dari stok.</div>
                        @elseif ($berikutnya === 'selesai' && $pesanan->sisaTagihan() > 0)
                            <div class="form-text teks-amber">Pelunasan @rupiah($pesanan->sisaTagihan()) belum masuk.</div>
                        @endif
                    </form>
                @elseif ($pesanan->status === 'selesai')
                    <p class="text-ink-2">Pesanan sudah diserahkan.</p>
                @else
                    <p class="text-ink-2">Pesanan dibatalkan.
                        @if ($pesanan->stokProdukJadi->isNotEmpty()) Buketnya masuk ke stok siap jual. @endif
                    </p>
                @endif

                @if ($isOwner && $sebelumnya)
                    <form method="POST" action="{{ route('admin.pesanan.status', $pesanan) }}" class="d-flex gap-2 mb-3">
                        @csrf
                        <select name="status" class="form-select" aria-label="Mundurkan ke">
                            @foreach (array_reverse($sebelumnya) as $s)
                                <option value="{{ $s }}">Mundurkan ke {{ strtolower(\App\Models\Pesanan::STATUS[$s][0]) }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-garis text-nowrap">Mundurkan</button>
                    </form>
                @endif

                @if (in_array($pesanan->status, \App\Models\Pesanan::STATUS_AKTIF))
                    <details>
                        <summary class="text-ink-2">Batalkan pesanan</summary>
                        <form method="POST" action="{{ route('admin.pesanan.status', $pesanan) }}" class="mt-2"
                              onsubmit="return confirm('Batalkan {{ $pesanan->kode }}? DP yang sudah masuk tidak dikembalikan.')">
                            @csrf
                            <input type="hidden" name="status" value="batal">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="masuk_stok_jadi" value="1" id="masuk_stok_jadi"
                                       @checked($pesanan->status !== 'masuk')>
                                <label class="form-check-label" for="masuk_stok_jadi">Buket sudah/terlanjur dibuat — masukkan ke Stok Produk Jadi</label>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small" for="harga_jual">Harga jual langsung (boleh kosong)</label>
                                <input type="number" min="0" step="500" name="harga_jual" id="harga_jual" class="form-control">
                            </div>
                            <p class="small text-ink-2">DP tetap tercatat sebagai pemasukan (hangus). Bahan yang sudah terpakai tidak dikembalikan ke stok.</p>
                            <button class="btn btn-garis">Ya, batalkan</button>
                        </form>
                    </details>
                @endif
            </x-panel>

            <x-panel judul="Pembayaran">
                <table class="table table-sm mb-3">
                    <tbody>
                    <tr><td>Harga {{ $item->qty }} × @rupiah($item->harga)</td><td class="angka">@rupiah($pesanan->subtotal)</td></tr>
                    @if ($pesanan->biaya_tambahan > 0)
                        <tr><td>Biaya tambahan</td><td class="angka">@rupiah($pesanan->biaya_tambahan)</td></tr>
                    @endif
                    @if ($pesanan->ongkir > 0)
                        <tr><td>Ongkir</td><td class="angka">@rupiah($pesanan->ongkir)</td></tr>
                    @endif
                    <tr class="fw-bold"><td>Total</td><td class="angka">@rupiah($pesanan->total)</td></tr>
                    @foreach ($pesanan->pembayaran as $b)
                        <tr class="text-ink-2">
                            <td>{{ $b->jenis === 'dp' ? 'DP' : 'Pelunasan' }} · {{ ['transfer' => 'Transfer', 'qris' => 'QRIS', 'cash' => 'Tunai'][$b->metode] }} · {{ $b->tanggal->translatedFormat('j M') }}</td>
                            <td class="angka">− @rupiah($b->jumlah)</td>
                        </tr>
                    @endforeach
                    <tr class="fw-bold"><td>Sisa tagihan</td><td class="angka">@rupiah($pesanan->sisaTagihan())</td></tr>
                    </tbody>
                </table>

                @if ($bisaBayar)
                    <form method="POST" action="{{ route('admin.pesanan.pembayaran', $pesanan) }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small" for="jumlah">{{ $pesanan->pembayaran->isEmpty() ? 'Jumlah (DP atau lunas)' : 'Pelunasan' }}</label>
                                <input type="number" min="1" step="500" id="jumlah" name="jumlah" class="form-control angka" required
                                       value="{{ old('jumlah', $pesanan->pembayaran->isEmpty() ? $pesanan->total / 2 : $pesanan->sisaTagihan()) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small" for="metode">Cara bayar</label>
                                <select id="metode" name="metode" class="form-select">
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">QRIS</option>
                                    <option value="cash">Tunai</option>
                                </select>
                            </div>
                            <input type="hidden" name="tanggal" value="{{ today()->toDateString() }}">
                            <div class="col-12"><button class="btn btn-garis w-100">Catat pembayaran</button></div>
                        </div>
                    </form>
                @endif
            </x-panel>
        </div>
    </div>
@endsection

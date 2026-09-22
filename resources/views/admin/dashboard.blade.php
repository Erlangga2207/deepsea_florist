@extends('layouts.admin')

@section('judul', 'Dashboard')

@section('konten')
    @php $jam = fn ($n) => rtrim(rtrim(number_format($n, 1, ',', '.'), '0'), ','); @endphp

    <div class="judul-halaman">
        <div>
            <h1>Halo, {{ auth()->user()->name }}</h1>
            <div class="text-ink-2">{{ today()->translatedFormat('l, j F Y') }}</div>
        </div>
        <a href="{{ route('admin.pesanan.create') }}" class="btn btn-primary">Tambah pesanan</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <x-tile label="Pesanan aktif" :nilai="$jumlahAktif" sub="masuk, dikerjakan, jadi" />
        </div>
        <div class="col-6 col-lg-3">
            <x-tile label="Jatuh tempo hari ini" :nilai="$jatuhTempo" sub="harus diserahkan hari ini" nada="amber" />
        </div>
        <div class="col-6 col-lg-3">
            <x-tile label="Beban hari ini" :nilai="$jam($bebanHariIni->beban).'/'.$jam($bebanHariIni->kapasitas)" sub="jam kerja perakit"
                    :nada="$bebanHariIni->warna() === 'hijau' ? null : $bebanHariIni->warna()" />
        </div>
        <div class="col-6 col-lg-3">
            <x-tile label="Bahan menipis" :nilai="$bahanMenipis->count()" sub="di bawah stok minimum" :nada="$bahanMenipis->isNotEmpty() ? 'merah' : null" />
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <x-panel judul="Antrian hari ini" :tautan="route('admin.pesanan.index')">
                @if ($antrian->isEmpty())
                    <p class="text-ink-2 mb-0">Tidak ada pesanan yang jatuh tempo hari ini.</p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                            @foreach ($antrian as $p)
                                @php [$tanggalTeks, $tanggalKelas] = $p->tanggalJadiRelatif(); [$bayarLabel, $bayarWarna] = $p->statusBayar(); @endphp
                                <tr>
                                    <td class="text-nowrap"><a href="{{ route('admin.pesanan.show', $p) }}" class="fw-semibold angka">{{ $p->kode }}</a></td>
                                    <td>
                                        {{ $p->item->first()?->nama_item }}
                                        <div class="small text-ink-2">{{ $p->pelanggan->nama }}@if ($p->catatan) · {{ Str::limit($p->catatan, 40) }}@endif</div>
                                    </td>
                                    <td class="text-nowrap {{ $tanggalKelas }}">{{ $tanggalTeks }}</td>
                                    <td class="text-nowrap"><x-pill :warna="$p->warnaStatus()" :label="$p->labelStatus()" /></td>
                                    <td class="text-nowrap"><x-pill :warna="$bayarWarna" :label="$bayarLabel" /></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-panel>

            <x-panel judul="Siap dijual langsung" :tautan="Route::has('admin.stok-produk-jadi.index') ? route('admin.stok-produk-jadi.index') : null">
                @forelse ($siapJual as $s)
                    <div class="saran-item">
                        <x-foto-produk :path="$s->foto" alt="" class="thumb" />
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $s->nama }}</div>
                            <div class="small text-ink-2">{{ $s->asal === 'batal' ? 'Dari pesanan batal' : 'Dibuat untuk dipajang' }} · sejak {{ $s->created_at->translatedFormat('j M') }}</div>
                        </div>
                        <div class="angka text-nowrap">@if ($s->harga_jual) @rupiah($s->harga_jual) @endif</div>
                    </div>
                @empty
                    <p class="text-ink-2 mb-0">Belum ada buket jadi yang siap dijual.</p>
                @endforelse
            </x-panel>
        </div>

        <div class="col-lg-5">
            <x-panel judul="Beban 7 hari ke depan">
                <ul class="list-unstyled mb-0 beban">
                    @foreach ($bebanMinggu as $hari)
                        <li>
                            <span class="beban-hari">{{ $hari->tanggal->translatedFormat('D j') }}</span>
                            <span class="beban-batang" aria-hidden="true">
                                <span class="beban-isi beban-{{ $hari->warna() }}" style="width: {{ min(100, $hari->persen()) }}%"></span>
                            </span>
                            <span class="beban-jam angka {{ $hari->warna() === 'merah' ? 'teks-merah' : ($hari->warna() === 'amber' ? 'teks-amber' : '') }}">
                                {{ $jam($hari->beban) }}/{{ $jam($hari->kapasitas) }} jam
                            </span>
                        </li>
                    @endforeach
                </ul>
            </x-panel>

            <x-panel judul="Bahan menipis" :tautan="route('admin.bahan.index')">
                @forelse ($bahanMenipis as $b)
                    @php [$label, $warna] = $b->statusStok(); @endphp
                    <div class="d-flex justify-content-between align-items-center py-1 gap-2">
                        <span>{{ $b->nama }}</span>
                        <span class="text-nowrap"><span class="angka">@angka($b->stok)/@angka($b->stok_minimum) {{ $b->satuan }}</span> <x-pill :warna="$warna" :label="$label" /></span>
                    </div>
                @empty
                    <p class="text-ink-2 mb-0">Semua bahan aman.</p>
                @endforelse
                @if ($bahanMenipis->isNotEmpty() && Route::has('admin.daftar-belanja'))
                    <a href="{{ route('admin.daftar-belanja') }}" class="btn btn-garis btn-sm mt-3">Buka daftar belanja</a>
                @endif
            </x-panel>
        </div>
    </div>
@endsection

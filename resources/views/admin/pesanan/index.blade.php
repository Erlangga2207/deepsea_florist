@extends('layouts.admin')

@section('judul', 'Pesanan')

@section('konten')
    @php
        $tabs = ['aktif' => ['Aktif', $jumlah->only(\App\Models\Pesanan::STATUS_AKTIF)->sum()]]
            + collect(\App\Models\Pesanan::STATUS)->map(fn ($s, $kunci) => [$s[0], $jumlah[$kunci] ?? 0])->all()
            + ['semua' => ['Semua', $jumlah->sum()]];
    @endphp

    <div class="judul-halaman">
        <h1>Pesanan</h1>
        <a href="{{ route('admin.pesanan.create') }}" class="btn btn-primary">Tambah pesanan</a>
    </div>

    <nav class="tab-garis">
        @foreach ($tabs as $kunci => [$label, $n])
            <a href="{{ route('admin.pesanan.index', ['tab' => $kunci]) }}" class="{{ $tab === $kunci ? 'aktif' : '' }}">
                {{ $label }} <span class="text-ink-2 fw-normal">{{ $n }}</span>
            </a>
        @endforeach
    </nav>

    <form method="GET" class="mb-3">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="search" name="cari" value="{{ request('cari') }}" class="form-control" style="max-width:300px" placeholder="Cari kode atau nama pemesan">
    </form>

    <x-panel>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pemesan</th>
                    <th>Model</th>
                    <th>Tanggal jadi</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th class="angka">Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($pesanan as $p)
                    @php
                        [$tanggalTeks, $tanggalKelas] = $p->tanggalJadiRelatif();
                        [$bayarLabel, $bayarWarna] = $p->statusBayar();
                        $item = $p->item->first();
                    @endphp
                    <tr>
                        <td class="text-nowrap"><a href="{{ route('admin.pesanan.show', $p) }}" class="fw-semibold angka">{{ $p->kode }}</a></td>
                        <td>{{ $p->pelanggan->nama }}</td>
                        <td>
                            {{ $item?->nama_item }}@if ($item && $item->qty > 1) × {{ $item->qty }}@endif
                            @if ($p->catatan)
                                <div class="small text-ink-2">{{ Str::limit($p->catatan, 50) }}</div>
                            @endif
                        </td>
                        <td class="text-nowrap {{ $tanggalKelas }}">{{ $tanggalTeks }}</td>
                        <td><x-pill :warna="$p->warnaStatus()" :label="$p->labelStatus()" /></td>
                        <td><x-pill :warna="$bayarWarna" :label="$bayarLabel" /></td>
                        <td class="angka text-nowrap">@rupiah($p->total)</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-ink-2 py-4">Tidak ada pesanan di sini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-panel>

    {{ $pesanan->links() }}
@endsection

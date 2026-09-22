@extends('layouts.admin')

@section('judul', 'Bahan & stok')

@section('konten')
    <div class="judul-halaman">
        <h1>Bahan & stok</h1>
        <div class="d-flex flex-wrap gap-2">
            @if (Route::has('admin.daftar-belanja'))
                <a href="{{ route('admin.daftar-belanja') }}" class="btn btn-garis">Daftar belanja</a>
            @endif
            <a href="{{ route('admin.bahan.create') }}" class="btn btn-garis">Tambah bahan</a>
            <a href="{{ route('admin.mutasi-stok.create') }}" class="btn btn-primary">Catat stok masuk/keluar</a>
        </div>
    </div>

    <nav class="tab-garis">
        <a href="{{ route('admin.bahan.index') }}" class="{{ request('jenis') ? '' : 'aktif' }}">Semua</a>
        @foreach ($jenis as $nilai => $label)
            <a href="{{ route('admin.bahan.index', ['jenis' => $nilai]) }}" class="{{ request('jenis') === $nilai ? 'aktif' : '' }}">{{ $label }}</a>
        @endforeach
    </nav>

    <x-panel>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Bahan</th>
                    <th>Jenis</th>
                    <th class="angka">Sisa</th>
                    <th class="angka">Minimum</th>
                    <th>Status</th>
                    <th>Masuk terakhir</th>
                    <th>Catatan umur</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($bahan as $b)
                    @php [$labelStatus, $warnaStatus] = $b->statusStok(); @endphp
                    <tr @class(['text-ink-2' => ! $b->is_aktif])>
                        <td>
                            <div class="fw-semibold">{{ $b->nama }}</div>
                            @if ($b->dijual_eceran)
                                <div class="small text-ink-2">Eceran @rupiah($b->harga_eceran) / {{ $b->satuan }}</div>
                            @endif
                            @unless ($b->is_aktif)
                                <div class="small">Nonaktif</div>
                            @endunless
                        </td>
                        <td>{{ $jenis[$b->jenis] }}</td>
                        <td class="angka text-nowrap">@angka($b->stok) {{ $b->satuan }}</td>
                        <td class="angka text-nowrap">@angka($b->stok_minimum) {{ $b->satuan }}</td>
                        <td><x-pill :warna="$warnaStatus" :label="$labelStatus" /></td>
                        <td class="text-nowrap">{{ $b->masukTerakhir?->tanggal->translatedFormat('j M Y') ?? '—' }}</td>
                        <td class="text-nowrap">{{ $b->catatanUmur() }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.mutasi-stok.create', ['bahan' => $b->id]) }}" class="btn btn-garis btn-sm">Catat</a>
                            <a href="{{ route('admin.bahan.edit', $b) }}" class="btn btn-polos btn-sm">Ubah</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-ink-2 py-4">Belum ada bahan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-panel>
@endsection

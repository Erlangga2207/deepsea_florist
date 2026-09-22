@extends('layouts.admin')

@section('judul', 'Mutasi stok')

@section('konten')
    <div class="judul-halaman">
        <h1>Riwayat mutasi stok</h1>
        <a href="{{ route('admin.mutasi-stok.create') }}" class="btn btn-primary">Catat stok</a>
    </div>

    <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
        <select name="bahan" class="form-select" style="max-width:260px" onchange="this.form.submit()">
            <option value="">Semua bahan</option>
            @foreach ($semuaBahan as $b)
                <option value="{{ $b->id }}" @selected(request('bahan') == $b->id)>{{ $b->nama }}</option>
            @endforeach
        </select>
        <select name="tipe" class="form-select" style="max-width:200px" onchange="this.form.submit()">
            <option value="">Semua jenis</option>
            @foreach (['masuk' => 'Masuk', 'keluar' => 'Keluar', 'penyesuaian' => 'Penyesuaian'] as $nilai => $label)
                <option value="{{ $nilai }}" @selected(request('tipe') === $nilai)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <x-panel>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Bahan</th>
                    <th>Jenis</th>
                    <th class="angka">Jumlah</th>
                    <th>Keterangan</th>
                    <th>Oleh</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($mutasi as $m)
                    <tr>
                        <td class="text-nowrap">{{ $m->tanggal->translatedFormat('j M Y') }}</td>
                        <td>{{ $m->bahan->nama }}</td>
                        <td>
                            @switch($m->tipe)
                                @case('masuk') <x-pill warna="hijau" label="Masuk" /> @break
                                @case('keluar') <x-pill warna="amber" label="Keluar" /> @break
                                @default <x-pill label="Penyesuaian" />
                            @endswitch
                        </td>
                        <td class="angka text-nowrap">
                            {{ ['masuk' => '+', 'keluar' => '−'][$m->tipe] ?? '' }}@angka($m->jumlah) {{ $m->bahan->satuan }}
                        </td>
                        <td>
                            {{ $m->keterangan }}
                            @if ($m->tanggal_kadaluarsa)
                                <div class="small text-ink-2">Layu sekitar {{ $m->tanggal_kadaluarsa->translatedFormat('j M') }}</div>
                            @endif
                        </td>
                        <td class="text-ink-2">{{ $m->user->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-ink-2 py-4">Belum ada catatan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-panel>

    {{ $mutasi->links() }}
@endsection
